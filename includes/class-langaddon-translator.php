<?php
/**
 * Translation + cache layer.
 * Strings are translated once via a machine-translation backend and stored in a
 * custom table, so repeat views are instant and every translation is editable.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Langaddon_Translator {

	const OPTION = 'langaddon_settings';

	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'langaddon_strings';
	}

	/** Create the cache table and seed default settings. Runs on activation. */
	public static function install() {
		global $wpdb;
		$table   = self::table();
		$charset = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( "CREATE TABLE $table (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lang VARCHAR(12) NOT NULL,
			hash CHAR(40) NOT NULL,
			source LONGTEXT NOT NULL,
			translation LONGTEXT NOT NULL,
			edited TINYINT(1) NOT NULL DEFAULT 0,
			updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY lang_hash (lang, hash)
		) $charset;" );

		if ( ! get_option( self::OPTION ) ) {
			add_option( self::OPTION, self::defaults() );
		}
	}

	public static function defaults() {
		return array(
			'source'        => 'en',
			'targets'       => array(),          // e.g. ['de','fr','ar']
			'provider'      => 'mymemory',        // mymemory | libretranslate | google | deepl
			'mymemory_email'=> '',                // raises the free daily limit
			'libre_url'     => 'https://libretranslate.com/translate',
			'libre_key'     => '',
			'google_key'    => '',
			'deepl_key'     => '',
			'deepl_free'    => 1,                  // 1 = api-free.deepl.com
			'per_request'   => 40,                // max NEW strings translated per pageload (rest fill on later views)
			'switcher'      => 'dropdown',        // dropdown | inline
			'float'         => 0,                 // show floating switcher bottom-corner
			'cache_head'    => 1,                 // translate <title> + meta description
			'protect_terms' => '',                // words/phrases kept verbatim in every language (one per line)
			'protect_prices'=> 0,                 // keep any text containing a currency amount untranslated
		);
	}

	public static function settings() {
		return wp_parse_args( get_option( self::OPTION, array() ), self::defaults() );
	}

	public static function hash( $text ) {
		return sha1( trim( preg_replace( '/\s+/u', ' ', $text ) ) );
	}

	/** The do-not-translate list from settings, longest phrase first. */
	public static function protected_terms() {
		$s   = self::settings();
		$raw = isset( $s['protect_terms'] ) ? (string) $s['protect_terms'] : '';
		$out = array();
		foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
			$line = trim( $line );
			if ( $line !== '' ) { $out[] = $line; }
		}
		usort( $out, function ( $a, $b ) { return mb_strlen( $b ) - mb_strlen( $a ); } );
		return $out;
	}

	/** True when a string should be left exactly as-is (a whole protected term, or a price when the guard is on). */
	protected static function should_skip( $text, array $terms, $s ) {
		$trim = trim( $text );
		foreach ( $terms as $term ) {
			if ( 0 === strcasecmp( $trim, $term ) ) { return true; }
		}
		if ( ! empty( $s['protect_prices'] ) && preg_match( '/[\$\x{20AC}\x{00A3}\x{20BF}]\s?\d/u', $text ) ) {
			return true;
		}
		return false;
	}

	/** Replace protected terms with neutral tokens before sending text to a backend. */
	protected static function mask_terms( $text, array $terms ) {
		$mask = array();
		if ( empty( $terms ) ) { return array( $text, $mask ); }
		$i = 0;
		foreach ( $terms as $term ) {
			if ( $term === '' ) { continue; }
			$token = '[[LAX' . $i . ']]';
			$count = 0;
			$text  = preg_replace_callback( '/' . preg_quote( $term, '/' ) . '/iu', function ( $m ) use ( &$mask, $token ) {
				$mask[ $token ] = $m[0];
				return $token;
			}, $text, -1, $count );
			if ( $count > 0 ) { $i++; }
		}
		return array( $text, $mask );
	}

	/** Put the original protected terms back after translation (tolerant of spacing/case the engine may add). */
	protected static function unmask_terms( $text, array $mask ) {
		foreach ( $mask as $token => $orig ) {
			$num  = preg_replace( '/\D/', '', $token );
			$text = preg_replace_callback( '/\[\[\s*LAX\s*' . $num . '\s*\]\]/i', function () use ( $orig ) { return $orig; }, $text );
			$text = str_replace( $token, $orig, $text );
		}
		return $text;
	}

	/** Bulk cache lookup: returns [hash => translation] for the ones we already have. */
	public static function get_cached( $lang, array $hashes ) {
		global $wpdb;
		if ( empty( $hashes ) ) { return array(); }
		$table = self::table();
		$in    = implode( ',', array_fill( 0, count( $hashes ), '%s' ) );
		$params = array_merge( array( $lang ), $hashes );
		$sql   = $wpdb->prepare( 'SELECT hash, translation FROM `' . esc_sql( $table ) . "` WHERE lang = %s AND hash IN ($in)", $params ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
		$rows  = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
		$out   = array();
		foreach ( (array) $rows as $r ) { $out[ $r['hash'] ] = $r['translation']; }
		return $out;
	}

	public static function store( $lang, $hash, $source, $translation, $edited = 0 ) {
		global $wpdb;
		$wpdb->replace( self::table(), array( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			'lang'        => $lang,
			'hash'        => $hash,
			'source'      => $source,
			'translation' => $translation,
			'edited'      => (int) $edited,
			'updated'     => current_time( 'mysql' ),
		), array( '%s', '%s', '%s', '%s', '%d', '%s' ) );
	}

	/**
	 * Translate a batch of unique source strings into $lang.
	 * Uses cache first; fetches up to $cap misses from the provider; returns [source => translation].
	 * Strings not translated this round are returned unchanged (they translate on a later view or via Warm).
	 */
	public static function translate_batch( array $sources, $lang, $cap ) {
		$s     = self::settings();
		$terms = self::protected_terms();
		$result = array();
		$hashes = array();
		foreach ( $sources as $src ) { $hashes[ $src ] = self::hash( $src ); }
		$cached = self::get_cached( $lang, array_values( $hashes ) );

		$fetched = 0;
		foreach ( $sources as $src ) {
			$h = $hashes[ $src ];
			if ( isset( $cached[ $h ] ) ) {
				$result[ $src ] = $cached[ $h ];
				continue;
			}
			if ( self::should_skip( $src, $terms, $s ) ) {
				self::store( $lang, $h, $src, $src );
				$result[ $src ] = $src;
				continue;
			}
			if ( $fetched >= $cap ) { $result[ $src ] = $src; continue; }
			list( $masked, $mask ) = self::mask_terms( $src, $terms );
			$t = self::provider_translate( $masked, $s['source'], $lang, $s );
			$fetched++;
			if ( $t !== null && $t !== '' ) {
				$t = self::unmask_terms( $t, $mask );
				self::store( $lang, $h, $src, $t );
				$result[ $src ] = $t;
			} else {
				$result[ $src ] = $src; // keep original on failure
			}
		}
		return $result;
	}

	/** Dispatch to the chosen backend. Returns translated string or null on failure. */
	public static function provider_translate( $text, $from, $to, $s ) {
		$to_e   = Langaddon_Languages::engine_code( $to );
		$from_e = Langaddon_Languages::engine_code( $from );
		switch ( $s['provider'] ) {
			case 'libretranslate': return self::via_libre( $text, $from_e, $to_e, $s );
			case 'google':         return self::via_google( $text, $from_e, $to_e, $s );
			case 'deepl':          return self::via_deepl( $text, $from_e, $to_e, $s );
			case 'mymemory':
			default:               return self::via_mymemory( $text, $from_e, $to_e, $s );
		}
	}

	protected static function via_mymemory( $text, $from, $to, $s ) {
		$args = array( 'q' => $text, 'langpair' => $from . '|' . $to );
		if ( ! empty( $s['mymemory_email'] ) ) { $args['de'] = $s['mymemory_email']; }
		$url  = add_query_arg( array_map( 'rawurlencode', $args ), 'https://api.mymemory.translated.net/get' );
		$res  = wp_remote_get( $url, array( 'timeout' => 15 ) );
		if ( is_wp_error( $res ) ) { return null; }
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		if ( isset( $body['responseData']['translatedText'] ) && (int) $body['responseStatus'] === 200 ) {
			$t = $body['responseData']['translatedText'];
			// MyMemory sometimes returns an error sentence in the field; guard against obvious quota strings.
			if ( stripos( $t, 'MYMEMORY WARNING' ) !== false || stripos( $t, 'QUERY LENGTH LIMIT' ) !== false ) { return null; }
			return html_entity_decode( $t, ENT_QUOTES, 'UTF-8' );
		}
		return null;
	}

	protected static function via_libre( $text, $from, $to, $s ) {
		$payload = array( 'q' => $text, 'source' => $from, 'target' => $to, 'format' => 'text' );
		if ( ! empty( $s['libre_key'] ) ) { $payload['api_key'] = $s['libre_key']; }
		$res = wp_remote_post( $s['libre_url'], array( 'timeout' => 20, 'body' => $payload ) );
		if ( is_wp_error( $res ) ) { return null; }
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		return isset( $body['translatedText'] ) ? $body['translatedText'] : null;
	}

	protected static function via_google( $text, $from, $to, $s ) {
		if ( empty( $s['google_key'] ) ) { return null; }
		$url = add_query_arg( 'key', $s['google_key'], 'https://translation.googleapis.com/language/translate/v2' );
		$res = wp_remote_post( $url, array( 'timeout' => 20, 'body' => array( 'q' => $text, 'source' => substr( $from, 0, 2 ), 'target' => substr( $to, 0, 2 ), 'format' => 'text' ) ) );
		if ( is_wp_error( $res ) ) { return null; }
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		return isset( $body['data']['translations'][0]['translatedText'] ) ? html_entity_decode( $body['data']['translations'][0]['translatedText'], ENT_QUOTES, 'UTF-8' ) : null;
	}

	protected static function via_deepl( $text, $from, $to, $s ) {
		if ( empty( $s['deepl_key'] ) ) { return null; }
		$host = ! empty( $s['deepl_free'] ) ? 'https://api-free.deepl.com' : 'https://api.deepl.com';
		$res  = wp_remote_post( $host . '/v2/translate', array(
			'timeout' => 20,
			'headers' => array( 'Authorization' => 'DeepL-Auth-Key ' . $s['deepl_key'] ),
			'body'    => array( 'text' => $text, 'target_lang' => strtoupper( substr( $to, 0, 2 ) ) ),
		) );
		if ( is_wp_error( $res ) ) { return null; }
		$body = json_decode( wp_remote_retrieve_body( $res ), true );
		return isset( $body['translations'][0]['text'] ) ? $body['translations'][0]['text'] : null;
	}
}

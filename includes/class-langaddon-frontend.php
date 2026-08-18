<?php
/**
 * Front-end: language routing, on-the-fly translation of the page (cached),
 * hreflang tags, RTL, and the language switcher.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Langaddon_Frontend {

	protected $current = 'en';
	protected $settings;
	protected $buffer_level = 0;

	public function __construct() {
		$this->settings = Langaddon_Translator::settings();
		$this->current  = $this->detect_language();

		add_shortcode( 'langaddon_switcher', array( $this, 'switcher_shortcode' ) );
		// Let themes that look for common switchers pick ours up too.
		add_shortcode( 'language-switcher', array( $this, 'switcher_shortcode' ) );

		add_filter( 'language_attributes', array( $this, 'language_attributes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_head', array( $this, 'hreflang' ), 1 );

		if ( ! empty( $this->settings['float'] ) ) {
			add_action( 'wp_footer', array( $this, 'float_switcher' ) );
		}

		if ( $this->should_translate() ) {
			add_action( 'template_redirect', array( $this, 'start_buffer' ), 0 );
		}
	}

	/* ---------------- language state ---------------- */

	public function current_language() { return $this->current; }

	protected function targets() {
		$t = array_values( array_filter( (array) $this->settings['targets'], array( 'Langaddon_Languages', 'exists' ) ) );
		return $t;
	}

	protected function detect_language() {
		$source  = $this->settings['source'];
		$allowed = array_merge( array( $source ), $this->targets() );
		// 1) explicit ?lang= (and remember it)
		if ( isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ?lang= is a read-only navigation preference, no form submitted.
			$l = str_replace( '_', '-', sanitize_key( wp_unslash( $_GET['lang'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ?lang= is a read-only navigation preference.
			if ( in_array( $l, $allowed, true ) ) {
				if ( ! headers_sent() ) {
					setcookie( 'langaddon_lang', $l, time() + YEAR_IN_SECONDS, defined( 'COOKIEPATH' ) ? COOKIEPATH : '/' );
				}
				return $l;
			}
		}
		// 2) cookie
		if ( isset( $_COOKIE['langaddon_lang'] ) ) {
			$l = sanitize_key( wp_unslash( $_COOKIE['langaddon_lang'] ) );
			if ( in_array( $l, $allowed, true ) ) { return $l; }
		}
		return $source;
	}

	protected function should_translate() {
		if ( is_admin() || is_feed() || wp_doing_ajax() ) { return false; }
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) { return false; }
		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) { return false; }
		return $this->current !== $this->settings['source'] && ! empty( $this->targets() );
	}

	/* ---------------- html translation ---------------- */

	public function start_buffer() {
		ob_start( array( $this, 'translate_html' ) );
		$this->buffer_level = ob_get_level();
		// Explicitly close our buffer at shutdown so it is never left open for another component to mishandle.
		add_action( 'shutdown', array( $this, 'close_buffer' ) );
	}

	/** Flush our page output buffer explicitly if it is still open at shutdown. */
	public function close_buffer() {
		if ( $this->buffer_level && ob_get_level() >= $this->buffer_level ) {
			ob_end_flush();
		}
	}

	public function translate_html( $html ) {
		if ( stripos( $html, '<html' ) === false || stripos( $html, '</body>' ) === false ) {
			return $html; // not a full HTML document
		}
		if ( ! class_exists( 'DOMDocument' ) ) { return $html; }

		$lang = $this->current;
		$prev = libxml_use_internal_errors( true );
		$dom  = new DOMDocument();
		$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR );
		libxml_clear_errors();
		libxml_use_internal_errors( $prev );

		$xpath = new DOMXPath( $dom );

		// Collect translatable text nodes (skip code / scripts / no-translate zones).
		$skip = 'not(ancestor::script) and not(ancestor::style) and not(ancestor::code) and not(ancestor::pre) '
			. 'and not(ancestor::textarea) and not(ancestor::noscript) and not(ancestor::*[@translate="no"]) '
			. 'and not(ancestor-or-self::*[contains(concat(" ",normalize-space(@class)," ")," notranslate ")]) '
			. 'and not(ancestor-or-self::*[contains(concat(" ",normalize-space(@class)," ")," langaddon-switcher ")])';
		$text_nodes = $xpath->query( '//body//text()[' . $skip . ']' );

		$attr_nodes = array();
		foreach ( array( '//img/@alt', '//input/@placeholder', '//textarea/@placeholder', '//*[@aria-label]/@aria-label', '//a/@title', '//input[@type="submit"]/@value' ) as $q ) {
			foreach ( $xpath->query( $q ) as $a ) { $attr_nodes[] = $a; }
		}

		// Head SEO strings (title + meta/OG). Kept separate so they are always translated on the
		// first view, independent of the per-page budget that paces the body text below.
		$head_nodes = array();
		if ( ! empty( $this->settings['cache_head'] ) ) {
			foreach ( $xpath->query( '//title/text()' ) as $a ) { $head_nodes[] = $a; }
			foreach ( $xpath->query( '//meta[@name="description"]/@content | //meta[@property="og:description"]/@content | //meta[@property="og:title"]/@content' ) as $a ) { $head_nodes[] = $a; }
		}

		// Normalise a node value to its lookup key (collapsed whitespace, must contain a letter).
		$key_of = function( $value ) {
			$t = trim( preg_replace( '/\s+/u', ' ', $value ) );
			if ( $t === '' || mb_strlen( $t ) > 4000 ) { return ''; }
			if ( ! preg_match( '/\p{L}/u', $t ) ) { return ''; } // must contain a letter
			return $t;
		};

		// Body strings fill in up to the per-page cap; head strings always translate in full.
		$body_uniq = array();
		foreach ( $text_nodes as $n ) { $k = $key_of( $n->nodeValue ); if ( $k !== '' ) { $body_uniq[ $k ] = true; } }
		foreach ( $attr_nodes as $n ) { $k = $key_of( $n->nodeValue ); if ( $k !== '' ) { $body_uniq[ $k ] = true; } }
		$head_uniq = array();
		foreach ( $head_nodes as $n ) { $k = $key_of( $n->nodeValue ); if ( $k !== '' ) { $head_uniq[ $k ] = true; } }
		if ( empty( $body_uniq ) && empty( $head_uniq ) ) { return $html; }

		$map = array();
		if ( ! empty( $head_uniq ) ) {
			$hk  = array_keys( $head_uniq );
			$map = Langaddon_Translator::translate_batch( $hk, $lang, count( $hk ) );
		}
		if ( ! empty( $body_uniq ) ) {
			$body_map = Langaddon_Translator::translate_batch( array_keys( $body_uniq ), $lang, (int) $this->settings['per_request'] );
			$map = $map + $body_map; // union; head translations win on any overlap
		}

		// Write translations back, preserving surrounding whitespace.
		foreach ( $text_nodes as $n ) {
			$orig = $n->nodeValue;
			$key  = $key_of( $orig );
			if ( $key === '' || ! isset( $map[ $key ] ) ) { continue; }
			$lead  = preg_match( '/^\s+/u', $orig, $m1 ) ? $m1[0] : '';
			$trail = preg_match( '/\s+$/u', $orig, $m2 ) ? $m2[0] : '';
			$n->nodeValue = $lead . $map[ $key ] . $trail;
		}
		foreach ( array_merge( $attr_nodes, $head_nodes ) as $n ) {
			$key = $key_of( $n->nodeValue );
			if ( $key !== '' && isset( $map[ $key ] ) ) { $n->nodeValue = $map[ $key ]; }
		}

		// SEO: point the translated page's canonical (and og:url) at itself, so search
		// engines index this language variant instead of folding it back to the source URL.
		foreach ( $xpath->query( '//link[@rel="canonical"]/@href | //meta[@property="og:url"]/@content' ) as $c ) {
			$c->nodeValue = add_query_arg( 'lang', $lang, $c->nodeValue );
		}

		// Localise the Open Graph locale (e.g. de_DE, tr_TR) so social-share previews match the language.
		$og_locale = Langaddon_Languages::og_locale( $lang );
		if ( $og_locale ) {
			foreach ( $xpath->query( '//meta[@property="og:locale"]/@content' ) as $c ) {
				$c->nodeValue = $og_locale;
			}
		}

		$out = $dom->saveHTML();
		$out = preg_replace( '/^<\?xml encoding="utf-8" \?>/', '', $out );
		return $out ? $out : $html;
	}

	/* ---------------- head: dir, lang, hreflang ---------------- */

	public function language_attributes( $output ) {
		$lang = $this->current;
		$dir  = Langaddon_Languages::is_rtl( $lang ) ? 'rtl' : 'ltr';
		$output = preg_replace( '/dir="(ltr|rtl)"/', '', $output );
		$output = preg_replace( '/lang="[^"]*"/', '', $output );
		return trim( $output . ' dir="' . $dir . '" lang="' . esc_attr( str_replace( '-', '_', $lang ) ) . '"' );
	}

	public function hreflang() {
		$source = $this->settings['source'];
		$langs  = array_merge( array( $source ), $this->targets() );
		$base   = home_url( add_query_arg( array() ) );
		foreach ( $langs as $l ) {
			$url = ( $l === $source ) ? remove_query_arg( 'lang', $base ) : add_query_arg( 'lang', $l, $base );
			printf( '<link rel="alternate" hreflang="%s" href="%s" />' . "\n", esc_attr( $l ), esc_url( $url ) );
		}
		printf( '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url( remove_query_arg( 'lang', $base ) ) );
	}

	/* ---------------- switcher ---------------- */

	public function switcher_shortcode( $atts = array() ) {
		$atts = shortcode_atts( array( 'style' => $this->settings['switcher'] ), $atts, 'langaddon_switcher' );
		return $this->render_switcher( $atts['style'] );
	}

	public function render_switcher( $style = 'dropdown' ) {
		$source  = $this->settings['source'];
		$langs   = array_merge( array( $source ), $this->targets() );
		if ( count( $langs ) < 2 ) { return ''; }
		$base    = home_url( add_query_arg( array() ) );

		ob_start();
		if ( 'inline' === $style ) {
			echo '<ul class="langaddon-switcher langaddon-switcher--inline notranslate">';
			foreach ( $langs as $l ) {
				$url = add_query_arg( 'lang', $l, $base );
				$cls = ( $l === $this->current ) ? 'is-active' : '';
				printf( '<li class="%s"><a href="%s" hreflang="%s">%s</a></li>', esc_attr( $cls ), esc_url( $url ), esc_attr( $l ), esc_html( Langaddon_Languages::name( $l ) ) );
			}
			echo '</ul>';
		} else {
			echo '<div class="langaddon-switcher langaddon-switcher--dropdown notranslate">';
			echo '<select onchange="if(this.value)window.location.href=this.value;" aria-label="' . esc_attr__( 'Choose language', 'langaddon' ) . '">';
			foreach ( $langs as $l ) {
				$url = add_query_arg( 'lang', $l, $base );
				// selected() is emitted inline in the option below.
				echo '<option value="' . esc_url( $url ) . '"' . selected( $l, $this->current, false ) . '>' . esc_html( Langaddon_Languages::name( $l ) ) . '</option>';
			}
			echo '</select></div>';
		}
		return ob_get_clean();
	}

	public function float_switcher() {
		echo '<div class="langaddon-float">' . $this->render_switcher( $this->settings['switcher'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- render_switcher() returns markup already escaped internally.
	}

	/* ---------------- assets ---------------- */

	public function assets() {
		wp_enqueue_style( 'langaddon', LANGADDON_URL . 'assets/css/langaddon.css', array(), LANGADDON_VER );
	}
}

<?php
/**
 * Admin settings screen (Settings → LangAddon).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Langaddon_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_post_langaddon_clear', array( $this, 'clear_cache' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( LANGADDON_FILE ), array( $this, 'action_links' ) );
	}

	/** Add a Settings link under the plugin on the Plugins screen. */
	public function action_links( $links ) {
		$url = admin_url( 'options-general.php?page=langaddon' );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'langaddon' ) . '</a>' );
		return $links;
	}

	public function menu() {
		add_options_page( 'LangAddon', 'LangAddon', 'manage_options', 'langaddon', array( $this, 'render' ) );
	}

	public function register() {
		register_setting( 'langaddon_group', Langaddon_Translator::OPTION, array( $this, 'sanitize' ) );
	}

	public function sanitize( $in ) {
		$d   = Langaddon_Translator::defaults();
		$out = Langaddon_Translator::settings();
		$out['source']         = ( isset( $in['source'] ) && Langaddon_Languages::exists( $in['source'] ) ) ? $in['source'] : 'en';
		$out['targets']        = array();
		if ( ! empty( $in['targets'] ) && is_array( $in['targets'] ) ) {
			foreach ( $in['targets'] as $t ) {
				if ( Langaddon_Languages::exists( $t ) && $t !== $out['source'] ) { $out['targets'][] = $t; }
			}
		}
		$out['provider']       = in_array( $in['provider'] ?? '', array( 'mymemory', 'libretranslate', 'google', 'deepl' ), true ) ? $in['provider'] : 'mymemory';
		$out['mymemory_email'] = sanitize_email( $in['mymemory_email'] ?? '' );
		$out['libre_url']      = esc_url_raw( $in['libre_url'] ?? $d['libre_url'] );
		$out['libre_key']      = sanitize_text_field( $in['libre_key'] ?? '' );
		$out['google_key']     = sanitize_text_field( $in['google_key'] ?? '' );
		$out['deepl_key']      = sanitize_text_field( $in['deepl_key'] ?? '' );
		$out['deepl_free']     = empty( $in['deepl_free'] ) ? 0 : 1;
		$out['per_request']    = max( 5, min( 300, (int) ( $in['per_request'] ?? 40 ) ) );
		$out['switcher']       = in_array( $in['switcher'] ?? '', array( 'dropdown', 'inline' ), true ) ? $in['switcher'] : 'dropdown';
		$out['float']          = empty( $in['float'] ) ? 0 : 1;
		$out['cache_head']     = empty( $in['cache_head'] ) ? 0 : 1;
		$out['protect_terms']  = isset( $in['protect_terms'] ) ? sanitize_textarea_field( $in['protect_terms'] ) : '';
		$out['protect_prices'] = empty( $in['protect_prices'] ) ? 0 : 1;
		return $out;
	}

	public function clear_cache() {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'langaddon_clear' ) ) { wp_die( 'No.' ); }
		global $wpdb;
		$t = Langaddon_Translator::table();
		$wpdb->query( 'TRUNCATE TABLE `' . esc_sql( $t ) . '`' ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
		wp_safe_redirect( add_query_arg( array( 'page' => 'langaddon', 'cleared' => 1 ), admin_url( 'options-general.php' ) ) );
		exit;
	}

	public function render() {
		$s   = Langaddon_Translator::settings();
		$all = Langaddon_Languages::all();
		global $wpdb;
		$count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM `' . esc_sql( Langaddon_Translator::table() ) . '`' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter
		?>
		<div class="wrap">
			<h1>LangAddon <span style="font-size:13px;color:#888;">v<?php echo esc_html( LANGADDON_VER ); ?></span></h1>
			<p>Free machine translation with editable, cached results. Pick your languages and a backend, then add the switcher with the <code>[langaddon_switcher]</code> shortcode (or enable the floating switcher).</p>
			<?php if ( isset( $_GET['cleared'] ) ) { echo '<div class="notice notice-success"><p>Translation cache cleared.</p></div>'; } // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice flag. ?>
			<form method="post" action="options.php">
				<?php settings_fields( 'langaddon_group' ); $o = Langaddon_Translator::OPTION; ?>
				<table class="form-table" role="presentation">
					<tr><th scope="row">Source language</th><td>
						<select name="<?php echo esc_attr( $o ); ?>[source]">
							<?php foreach ( $all as $code => $l ) { printf( '<option value="%s"%s>%s (%s)</option>', esc_attr( $code ), selected( $code, $s['source'], false ), esc_html( $l[1] ), esc_html( $code ) ); } ?>
						</select>
						<p class="description">The language your content is written in.</p>
					</td></tr>

					<tr><th scope="row">Translate into</th><td>
						<div style="column-count:3;max-width:760px;">
						<?php foreach ( $all as $code => $l ) {
							if ( $code === $s['source'] ) { continue; }
							// Checkbox state is emitted inline in the echo below.
							echo '<label style="display:block;"><input type="checkbox" name="' . esc_attr( $o ) . '[targets][]" value="' . esc_attr( $code ) . '"' . checked( in_array( $code, (array) $s['targets'], true ), true, false ) . '> ' . esc_html( $l[1] ) . ' <span style="color:#999;">' . esc_html( $code ) . esc_html( $l[2] ? ' · RTL' : '' ) . '</span></label>';
						} ?>
						</div>
					</td></tr>

					<tr><th scope="row">Translation backend</th><td>
						<select name="<?php echo esc_attr( $o ); ?>[provider]" id="ol-provider">
							<option value="mymemory"<?php selected( 'mymemory', $s['provider'] ); ?>>MyMemory: free, no key (rate-limited)</option>
							<option value="libretranslate"<?php selected( 'libretranslate', $s['provider'] ); ?>>LibreTranslate: free / self-hosted</option>
							<option value="google"<?php selected( 'google', $s['provider'] ); ?>>Google Cloud Translation: your API key</option>
							<option value="deepl"<?php selected( 'deepl', $s['provider'] ); ?>>DeepL: your API key</option>
						</select>
						<p class="description">MyMemory works with no setup. For bigger sites, self-host <a href="https://github.com/LibreTranslate/LibreTranslate" target="_blank" rel="noopener">LibreTranslate</a> or add a Google/DeepL key.</p>
					</td></tr>

					<tr><th scope="row">MyMemory email (optional)</th><td>
						<input type="email" name="<?php echo esc_attr( $o ); ?>[mymemory_email]" value="<?php echo esc_attr( $s['mymemory_email'] ); ?>" class="regular-text" placeholder="you@example.com">
						<p class="description">Raises MyMemory's free daily word limit.</p>
					</td></tr>
					<tr><th scope="row">LibreTranslate URL</th><td>
						<input type="url" name="<?php echo esc_attr( $o ); ?>[libre_url]" value="<?php echo esc_attr( $s['libre_url'] ); ?>" class="regular-text">
						&nbsp;Key: <input type="text" name="<?php echo esc_attr( $o ); ?>[libre_key]" value="<?php echo esc_attr( $s['libre_key'] ); ?>" class="regular-text" placeholder="(optional)">
					</td></tr>
					<tr><th scope="row">Google API key</th><td><input type="text" name="<?php echo esc_attr( $o ); ?>[google_key]" value="<?php echo esc_attr( $s['google_key'] ); ?>" class="regular-text"></td></tr>
					<tr><th scope="row">DeepL API key</th><td>
						<input type="text" name="<?php echo esc_attr( $o ); ?>[deepl_key]" value="<?php echo esc_attr( $s['deepl_key'] ); ?>" class="regular-text">
						<label style="margin-left:10px;"><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[deepl_free]" value="1"<?php checked( 1, $s['deepl_free'] ); ?>> Free API</label>
					</td></tr>

					<tr><th scope="row">Switcher style</th><td>
						<select name="<?php echo esc_attr( $o ); ?>[switcher]">
							<option value="dropdown"<?php selected( 'dropdown', $s['switcher'] ); ?>>Dropdown</option>
							<option value="inline"<?php selected( 'inline', $s['switcher'] ); ?>>Inline list</option>
						</select>
						<label style="margin-left:14px;"><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[float]" value="1"<?php checked( 1, $s['float'] ); ?>> Show floating switcher (bottom corner)</label>
					</td></tr>

					<tr><th scope="row">Never translate</th><td>
						<textarea name="<?php echo esc_attr( $o ); ?>[protect_terms]" rows="4" class="large-text code" placeholder="HostAddon&#10;cPanel&#10;.org&#10;.com"><?php echo esc_textarea( $s['protect_terms'] ); ?></textarea>
						<p class="description">One word or phrase per line. These stay exactly as written in every language, wherever they appear (brand names, product names, domain extensions, and so on). You can also add <code>class="notranslate"</code> or <code>translate="no"</code> to any element in your theme.</p>
						<label><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[protect_prices]" value="1"<?php checked( 1, $s['protect_prices'] ); ?>> Keep prices untranslated (any text containing a $, &euro;, &pound; or &#8383; amount)</label>
					</td></tr>

					<tr><th scope="row">Advanced</th><td>
						<label><input type="checkbox" name="<?php echo esc_attr( $o ); ?>[cache_head]" value="1"<?php checked( 1, $s['cache_head'] ); ?>> Translate page title &amp; meta description (SEO)</label><br>
						<label>New strings per page load: <input type="number" name="<?php echo esc_attr( $o ); ?>[per_request]" value="<?php echo (int) $s['per_request']; ?>" min="5" max="300" style="width:90px;"></label>
						<p class="description">The first visit to a page in a new language translates this many strings; the rest fill in on later views. Raise it for faster first-loads, lower it if your host times out.</p>
					</td></tr>
				</table>
				<?php submit_button(); ?>
			</form>

			<hr>
			<p><strong><?php echo esc_html( number_format_i18n( $count ) ); ?></strong> translated strings cached.
			Add the switcher anywhere with <code>[langaddon_switcher]</code>.</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Clear all cached translations?');">
				<input type="hidden" name="action" value="langaddon_clear">
				<?php wp_nonce_field( 'langaddon_clear' ); ?>
				<?php submit_button( 'Clear translation cache', 'delete', 'submit', false ); ?>
			</form>
			<p style="margin-top:26px;color:#8a8f98;font-size:12px;">
				A free tool by <a href="https://www.hostaddon.com" target="_blank" rel="noopener">HostAddon</a>, free tools for the hosting community. Use it, fork it, share it.
			</p>
		</div>
		<?php
	}
}

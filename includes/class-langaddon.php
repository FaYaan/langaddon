<?php
/**
 * Bootstrap: loads text domain and wires up admin + front-end.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Langaddon {

	protected static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) { self::$instance = new self(); }
		return self::$instance;
	}

	private function __construct() {
		// Translations load automatically since WordPress 4.6; no load_plugin_textdomain() call is needed.
		if ( is_admin() ) {
			new Langaddon_Admin();
		}
		// Front-end runs on admin too only for shortcode registration safety; guard inside.
		if ( ! is_admin() ) {
			new Langaddon_Frontend();
		}
	}
}

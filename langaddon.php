<?php
/**
 * Plugin Name:       LangAddon: Free Website Translation
 * Plugin URI:        https://github.com/FaYaan/langaddon
 * Description:       Free, self-hosted multilingual for WordPress. Machine-translates your site into 50+ languages via free backends (MyMemory / LibreTranslate) or your own Google/DeepL key, caches the results in your database, and lets you edit any translation. Includes a language switcher, RTL support and hreflang tags. No subscription, no lock-in.
 * Version:           1.1.2
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            HostAddon (Fayanka Tech Addon)
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       langaddon
 * Domain Path:       /languages
 *
 * LangAddon is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later version.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'LANGADDON_VER', '1.1.2' );
define( 'LANGADDON_FILE', __FILE__ );
define( 'LANGADDON_DIR', plugin_dir_path( __FILE__ ) );
define( 'LANGADDON_URL', plugin_dir_url( __FILE__ ) );

require_once LANGADDON_DIR . 'includes/class-langaddon-languages.php';
require_once LANGADDON_DIR . 'includes/class-langaddon-translator.php';
require_once LANGADDON_DIR . 'includes/class-langaddon-frontend.php';
require_once LANGADDON_DIR . 'includes/class-langaddon-admin.php';
require_once LANGADDON_DIR . 'includes/class-langaddon.php';

// Boot.
add_action( 'plugins_loaded', array( 'Langaddon', 'instance' ) );

// Activation: create the translation cache table + defaults.
register_activation_hook( __FILE__, array( 'Langaddon_Translator', 'install' ) );

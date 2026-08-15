<?php
/**
 * Language catalogue: code => [native name, English name, rtl].
 * Codes follow the common MyMemory / ISO-639-1 two-letter form (with a couple of
 * regional variants). Used for the switcher, the settings screen and hreflang.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Langaddon_Languages {

	public static function all() {
		return array(
			'en'    => array( 'English', 'English', false ),
			'ar'    => array( 'العربية', 'Arabic', true ),
			'az'    => array( 'Azərbaycan', 'Azerbaijani', false ),
			'bg'    => array( 'Български', 'Bulgarian', false ),
			'bn'    => array( 'বাংলা', 'Bengali', false ),
			'ca'    => array( 'Català', 'Catalan', false ),
			'cs'    => array( 'Čeština', 'Czech', false ),
			'da'    => array( 'Dansk', 'Danish', false ),
			'de'    => array( 'Deutsch', 'German', false ),
			'el'    => array( 'Ελληνικά', 'Greek', false ),
			'es'    => array( 'Español', 'Spanish', false ),
			'et'    => array( 'Eesti', 'Estonian', false ),
			'fa'    => array( 'فارسی', 'Persian', true ),
			'fi'    => array( 'Suomi', 'Finnish', false ),
			'fr'    => array( 'Français', 'French', false ),
			'he'    => array( 'עברית', 'Hebrew', true ),
			'hi'    => array( 'हिन्दी', 'Hindi', false ),
			'hr'    => array( 'Hrvatski', 'Croatian', false ),
			'hu'    => array( 'Magyar', 'Hungarian', false ),
			'id'    => array( 'Indonesia', 'Indonesian', false ),
			'it'    => array( 'Italiano', 'Italian', false ),
			'ja'    => array( '日本語', 'Japanese', false ),
			'ko'    => array( '한국어', 'Korean', false ),
			'lt'    => array( 'Lietuvių', 'Lithuanian', false ),
			'lv'    => array( 'Latviešu', 'Latvian', false ),
			'mk'    => array( 'Македонски', 'Macedonian', false ),
			'ms'    => array( 'Melayu', 'Malay', false ),
			'nb'    => array( 'Norsk', 'Norwegian', false ),
			'nl'    => array( 'Nederlands', 'Dutch', false ),
			'pl'    => array( 'Polski', 'Polish', false ),
			'pt-br' => array( 'Português (BR)', 'Portuguese (Brazil)', false ),
			'pt-pt' => array( 'Português', 'Portuguese (Portugal)', false ),
			'ro'    => array( 'Română', 'Romanian', false ),
			'ru'    => array( 'Русский', 'Russian', false ),
			'sk'    => array( 'Slovenčina', 'Slovak', false ),
			'sl'    => array( 'Slovenščina', 'Slovenian', false ),
			'sr'    => array( 'Српски', 'Serbian', false ),
			'sv'    => array( 'Svenska', 'Swedish', false ),
			'th'    => array( 'ไทย', 'Thai', false ),
			'tr'    => array( 'Türkçe', 'Turkish', false ),
			'uk'    => array( 'Українська', 'Ukrainian', false ),
			'vi'    => array( 'Tiếng Việt', 'Vietnamese', false ),
			'zh-cn' => array( '简体中文', 'Chinese (Simplified)', false ),
			'zh-tw' => array( '繁體中文', 'Chinese (Traditional)', false ),
		);
	}

	public static function exists( $code ) {
		$all = self::all();
		return isset( $all[ $code ] );
	}

	public static function name( $code ) {
		$all = self::all();
		return isset( $all[ $code ] ) ? $all[ $code ][0] : $code;
	}

	public static function is_rtl( $code ) {
		$all = self::all();
		return isset( $all[ $code ] ) ? (bool) $all[ $code ][2] : false;
	}

	/** MyMemory / most engines want a plain 2-letter source/target; map regional codes. */
	public static function engine_code( $code ) {
		$map = array( 'pt-br' => 'pt-BR', 'pt-pt' => 'pt-PT', 'zh-cn' => 'zh-CN', 'zh-tw' => 'zh-TW', 'nb' => 'no' );
		return isset( $map[ $code ] ) ? $map[ $code ] : $code;
	}
}

=== LangAddon: Free Website Translation ===
Contributors: hostaddon
Tags: translation, multilingual, translate, language switcher, hreflang, rtl, localization
Requires at least: 5.6
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free, self-hosted multilingual for WordPress. Machine-translate your site into 40+ languages, cache the results in your own database, and edit any translation.

== Description ==

LangAddon makes any WordPress site multilingual for free. It machine-translates your pages using free backends (MyMemory or a self-hosted LibreTranslate) or your own Google/DeepL key, caches every translation in your own database (so repeat views are instant and each string is translated only once), and lets you edit translations. Includes a language switcher, right-to-left support and hreflang tags.

No subscription. No third-party lock-in. Your translations stay in your database.

= Highlights =
* 40+ languages including RTL (Arabic, Farsi, Hebrew)
* Free backends (MyMemory, LibreTranslate) or your own Google/DeepL key
* Self-hosted, cached, editable translations
* Language switcher shortcode `[langaddon_switcher]`, floating widget, or theme slot
* hreflang tags + translated title/meta description

== Installation ==
1. Upload the plugin to `/wp-content/plugins/langaddon` or install the zip via Plugins → Add New → Upload.
2. Activate.
3. Go to Settings → LangAddon, choose your languages and a backend, and save.
4. Add `[langaddon_switcher]` where you want the switcher, or enable the floating switcher.

== Frequently Asked Questions ==

= Is it really free? =
Yes. The default MyMemory backend needs no key. For volume, self-host LibreTranslate (also free) or add your own Google/DeepL key.

= Are translations good for SEO? =
It outputs hreflang tags and translates titles/meta descriptions. Pretty sub-directory URLs are on the roadmap for stronger SEO.

= Can I edit a translation? =
Yes, cached translations are stored in your database and can be edited (a front-end editor is on the roadmap).

== Changelog ==
= 1.0.0 =
* Initial release.

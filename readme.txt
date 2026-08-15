=== LangAddon: Free Website Translation ===
Contributors: hostaddon
Tags: translation, multilingual, translate, language switcher, hreflang, rtl, localization
Requires at least: 5.6
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 1.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free, self-hosted multilingual for WordPress. Machine-translate your site into 40+ languages, cache the results in your own database, and edit any translation.

== Description ==

LangAddon makes any WordPress site multilingual for free. It machine-translates your pages using free backends (MyMemory or a self-hosted LibreTranslate) or your own Google/DeepL key, caches every translation in your own database (so repeat views are instant and each string is translated only once), and lets you edit translations. Includes a language switcher, right-to-left support and hreflang tags.

You can also keep chosen words untranslated everywhere: brand names, product names, domain extensions, code, and optionally anything that contains a price. See the "Never translate" question below.

No subscription. No third-party lock-in. Your translations stay in your database. Free for anyone to use, fork, and build on.

= Highlights =
* 40+ languages including RTL (Arabic, Farsi, Hebrew)
* Free backends (MyMemory, LibreTranslate) or your own Google/DeepL key
* Self-hosted, cached, editable translations
* Never-translate list for brand and product names, domains, and code, plus an optional price guard
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

= Can I stop certain words from being translated? =
Yes. Under Settings → LangAddon → Never translate, add one word or phrase per line (brand names, product names, domain extensions like .org, and so on). They stay exactly as written in every language, even in the middle of a sentence. You can also turn on the price guard to leave any text containing a currency amount untranslated, or mark elements in your theme with class="notranslate" or translate="no".

= Are translations good for SEO? =
It outputs hreflang tags and translates titles/meta descriptions. Pretty sub-directory URLs are on the roadmap for stronger SEO.

= Can I edit a translation? =
Yes, cached translations are stored in your database and can be edited (a front-end editor is on the roadmap).

== Changelog ==
= 1.1.0 =
* New: "Never translate" list to keep chosen words and phrases identical in every language, even inside sentences.
* New: optional price guard that leaves any text containing a currency amount untranslated.

= 1.0.0 =
* Initial release.

=== LangAddon: Free Website Translation ===
Contributors: hostaddon
Tags: translation, multilingual, translate, language switcher, hreflang
Requires at least: 5.6
Tested up to: 7.0
Requires PHP: 7.2
Stable tag: 1.1.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free, self-hosted multilingual for WordPress: translate your site into 40+ languages, cache them in your database, and edit any translation.

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

== Screenshots ==

1. The language switcher on the front end. Pages are served fully translated (shown here in German), with brand names, domains, and prices left exactly as written.
2. Settings: pick your source language, the languages to translate into, and a translation backend (MyMemory, LibreTranslate, Google, or DeepL).
3. The Never-translate list and the price guard, plus the per-page translation control.

== Frequently Asked Questions ==

= Is it really free? =
Yes. The default MyMemory backend needs no key. For volume, self-host LibreTranslate (also free) or add your own Google/DeepL key.

= Can I stop certain words from being translated? =
Yes. Under Settings → LangAddon → Never translate, add one word or phrase per line (brand names, product names, domain extensions like .org, and so on). They stay exactly as written in every language, even in the middle of a sentence. You can also turn on the price guard to leave any text containing a currency amount untranslated, or mark elements in your theme with class="notranslate" or translate="no".

= Are translations good for SEO? =
It outputs hreflang tags and translates titles/meta descriptions. Pretty sub-directory URLs are on the roadmap for stronger SEO.

= Can I edit a translation? =
Yes, cached translations are stored in your database and can be edited (a front-end editor is on the roadmap).

== External services ==

To translate text, LangAddon sends the strings on the page being viewed to the translation backend you select in Settings → LangAddon. Nothing is sent until you choose a backend and a target language, and results are cached in your own database so each string is sent only once. No data is sent to HostAddon.

Depending on your choice, text is sent to one of these services:

* MyMemory (default): sends the text to be translated, plus your email only if you add one to raise the free limit. Service by Translated. Docs and terms: https://mymemory.translated.net/doc/ , privacy: https://translated.com/privacy-policy/
* LibreTranslate (optional): sends the text to the LibreTranslate server URL you configure, which may be your own self-hosted server or a public one. Project: https://libretranslate.com/ . Terms and privacy depend on the server you point to; self-hosting keeps all text on your own infrastructure.
* Google Cloud Translation (optional): sends the text and your API key to Google. Terms: https://cloud.google.com/terms/ , privacy: https://policies.google.com/privacy
* DeepL (optional): sends the text and your API key to DeepL. Terms: https://www.deepl.com/pro-license , privacy: https://www.deepl.com/privacy

== Changelog ==
= 1.1.4 =
* Added a Screenshots section to the readme. No code changes.

= 1.1.3 =
* Hardening for the WordPress.org review: escaped all settings-screen output, guarded the database calls on the plugin's own cache table, sanitized the language query variable earlier, and dropped the deprecated text-domain loader. No functional changes.

= 1.1.2 =
* SEO: the page title and meta description (and Open Graph title/description) now translate on the first view of a page, instead of waiting behind the per-page translation budget.

= 1.1.1 =
* SEO: translated pages now set a self-referencing canonical (and og:url) so search engines index each language instead of folding it back to the source URL.

= 1.1.0 =
* New: "Never translate" list to keep chosen words and phrases identical in every language, even inside sentences.
* New: optional price guard that leaves any text containing a currency amount untranslated.

= 1.0.0 =
* Initial release.

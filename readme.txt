=== LangAddon Multilingual Translation ===
Contributors: hostaddon
Tags: translation, translate, multilingual, language switcher, localization
Requires at least: 5.6
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 1.3.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Translate your WordPress site into 40+ languages with a language switcher. Free, self-hosted machine translation you can cache and edit.

== Description ==

LangAddon is a free, self-hosted multilingual and translation plugin for WordPress. It automatically translates your website into 40+ languages, adds a language switcher, and stores every translation in your own database, so repeat views are instant and you can edit any translation by hand.

Unlike widget-based translators, LangAddon translates on the server. Search engines see fully translated pages, with hreflang tags, translated titles and meta descriptions, self-referencing canonicals, and right-to-left (RTL) support built in.

= Highlights =
* Automatic machine translation into 40+ languages, including RTL (Arabic, Hebrew, Persian).
* Free backends with no key: MyMemory, or a self-hosted LibreTranslate server.
* Bring your own key: Google Cloud Translation or DeepL for higher quality and volume.
* Self-hosted and cached: each string is translated once, stored in your database, and fully editable.
* Language switcher: the [langaddon_switcher] shortcode, a floating widget, or your theme's switcher slot.
* SEO-friendly: hreflang alternates, self-referencing canonical and og:url, translated title and meta description, and localized Open Graph locale.
* Never-translate list: keep brand names, product names, domain extensions, code, and prices identical in every language.
* WooCommerce-aware: cart and checkout AJAX fragments (mini-cart and order review) are translated too, and prices stay untouched.
* No subscription, no per-word fees, no third-party lock-in. Your translations stay in your database.

= How it works =

On a non-default language, LangAddon captures the rendered HTML, translates the visible text via your chosen backend, caches the result in your database, and writes it back while preserving your markup. Cached strings are served instantly and never hit the translation service again. The first visit to a page translates a batch of strings and the rest fill in on later views, so no single request times out.

= Supported translation services =

Choose one backend in the settings:
* MyMemory: free, no API key required (rate-limited; add an email to raise the limit).
* LibreTranslate: free and open source, self-host it for unlimited private translation.
* Google Cloud Translation: your own API key, wide language coverage.
* DeepL: your own API key, high quality for major languages.

== Installation ==
1. In your dashboard, go to Plugins, Add New, and search for "LangAddon", or upload the zip via Plugins, Add New, Upload.
2. Activate the plugin.
3. Go to Settings, LangAddon, choose your source language, the languages to translate into, and a backend, then save.
4. Add the [langaddon_switcher] shortcode where you want the switcher, or enable the floating switcher.

== Screenshots ==

1. The language switcher on the front end. Pages are served fully translated (shown here in German), with brand names, domains, and prices left exactly as written.
2. Settings: pick your source language, the languages to translate into, and a translation backend (MyMemory, LibreTranslate, Google, or DeepL).
3. The Never-translate list and the price guard, plus the per-page translation control.

== Frequently Asked Questions ==

= How do I translate my WordPress site? =
Install and activate LangAddon, go to Settings, LangAddon, pick your source language and the languages to translate into, choose a backend (MyMemory needs no key), and save. Then add the [langaddon_switcher] shortcode or turn on the floating switcher so visitors can change language.

= Is it really free? =
Yes. The default MyMemory backend needs no key. For volume, self-host LibreTranslate (also free) or add your own Google or DeepL key.

= Which translation services are supported? =
MyMemory (free, no key), a self-hosted LibreTranslate server, Google Cloud Translation, or DeepL (with your own API key).

= Does it work with WooCommerce? =
Yes. Shop, product, cart, and checkout pages translate like any other page, and the AJAX mini-cart and checkout order-review fragments are translated too, so dynamic updates stay in the right language. Prices are left untouched. The block-based (Store API) cart and checkout, and WooCommerce emails, are not translated yet.

= Is this an alternative to paid multilingual plugins? =
Yes. LangAddon gives you automatic translation, a language switcher, caching, and editable translations, self-hosted and free, without a subscription.

= Can I stop certain words from being translated? =
Yes. Under Settings, LangAddon, Never translate, add one word or phrase per line (brand names, product names, domain extensions like .org, and so on). They stay exactly as written in every language, even in the middle of a sentence. You can also turn on the price guard to leave any text containing a currency amount untranslated, or mark elements in your theme with class="notranslate" or translate="no".

= Is it good for SEO? =
Yes. LangAddon translates server-side and outputs hreflang alternates, a self-referencing canonical and og:url, translated title and meta description, and a localized Open Graph locale, so each language can be indexed on its own.

= Can I edit a translation? =
Yes, cached translations are stored in your database and can be edited (a front-end editor is on the roadmap).

== External services ==

To translate text, LangAddon sends the strings on the page being viewed to the translation backend you select in Settings, LangAddon. Nothing is sent until you choose a backend and a target language, and results are cached in your own database so each string is sent only once. No data is sent to HostAddon.

Depending on your choice, text is sent to one of these services:

* MyMemory (default): sends the text to be translated, plus your email only if you add one to raise the free limit. Service by Translated. Terms and privacy: https://mymemory.translated.net/terms-and-conditions
* LibreTranslate (optional): sends the text to the LibreTranslate server URL you configure, which may be your own self-hosted server or a public one. Project: https://libretranslate.com/ . Terms and privacy depend on the server you point to; self-hosting keeps all text on your own infrastructure.
* Google Cloud Translation (optional): sends the text and your API key to Google. Terms: https://cloud.google.com/terms/ , privacy: https://policies.google.com/privacy
* DeepL (optional): sends the text and your API key to DeepL. Terms: https://www.deepl.com/pro-license , privacy: https://www.deepl.com/privacy

== Changelog ==
= 1.3.0 =
* New: WooCommerce support. The AJAX mini-cart and checkout order-review fragments are now translated, so they update in the visitor's language. Prices stay untouched.

= 1.2.0 =
* New: a "Settings" link now appears under the plugin on the Plugins screen.
* Improved: rewritten description, FAQ, and tags for clarity and discoverability. No functional changes to translation.

= 1.1.9 =
* Removed the unprefixed [language-switcher] shortcode alias so every registered name uses the langaddon prefix. Use [langaddon_switcher].

= 1.1.8 =
* Renamed the plugin to "LangAddon Multilingual Translation".
* Fixed a broken link in the external services list, and the page output buffer is now closed explicitly on shutdown.

= 1.1.7 =
* Fixed: the target-language checkboxes emit their checked state inline, clearing the last Plugin Check output-escaping error. No functional changes.

= 1.1.6 =
* Fixed: escaped one settings-screen label flagged by Plugin Check. No functional changes.

= 1.1.5 =
* Fixed: the language switcher can now switch back to the source language (selecting it previously left the last language's cookie in place, so the page stayed translated).
* Improved: on translated pages the Open Graph locale is now localised (for example de_DE, tr_TR, ar_AR) for better social-share previews.

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

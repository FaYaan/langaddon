# LangAddon: Free Website Translation for WordPress

Make any WordPress site multilingual **for free**. LangAddon machine-translates your pages into 40+ languages using **free, no-lock-in backends** (MyMemory or a self-hosted LibreTranslate, or your own Google/DeepL key), **caches every translation in your own database**, and lets you **edit any string**. Ships with a language switcher, right-to-left support and `hreflang` tags.

Built and open-sourced by [HostAddon](https://www.hostaddon.com), and free for anyone to use, fork, and build on. GPL-licensed, no subscription, no lock-in.

## Why

Most WordPress translation plugins are either paid (TranslatePress, WPML, Weglot) or send every visitor through a third-party widget with no SEO and no way to fix a bad translation (GTranslate free). LangAddon aims for the honest middle: **free, self-hosted, cached, and editable.**

## Features

- 🌍 **40+ languages**, including RTL (Arabic, Farsi, Hebrew).
- 🆓 **No paid API required**: defaults to MyMemory (no key). Optional LibreTranslate (self-host), Google or DeepL.
- 💾 **Self-hosted cache**: each string is translated once and stored in your DB; repeat views are instant.
- ✍️ **Editable**: every cached translation lives in your database (a front-end editor is on the roadmap; for now edit rows directly or via the filter).
- 🛑 **Never-translate list**: keep brand names, product names, domain extensions, and code identical in every language, even mid-sentence. Optional price guard leaves any text with a currency amount (`$`, `€`, `£`, `₿`) untouched.
- 🔀 **Language switcher**: shortcode `[langaddon_switcher]`, a floating widget, or your theme's switcher slot.
- 🔎 **SEO basics**: `hreflang` alternate tags and translated `<title>` / meta description.
- ↔️ **RTL aware**: sets `dir="rtl"` and language attributes automatically.
- 🪶 **Lightweight**: one small stylesheet, no page builder, no external JS widget.

## Install

1. Download the latest release `.zip` (or clone this repo into `wp-content/plugins/langaddon`).
2. **Plugins → Add New → Upload Plugin**, activate.
3. **Settings → LangAddon**: choose your source language, tick the languages to translate into, pick a backend, save.
4. Add the switcher: drop `[langaddon_switcher]` into a menu/widget/template, or enable the floating switcher.

## Screenshots

Placeholder references; add real images at these paths before a release.

1. Settings → LangAddon: languages and backend. `docs/screenshot-1.png`
2. The Never-translate list and price guard. `docs/screenshot-2.png`
3. Front-end language switcher. `docs/screenshot-3.png`

## How it works

On a non-default language, LangAddon buffers the rendered HTML, walks the visible text nodes (skipping `<script>`, `<style>`, `<code>`, and anything marked `class="notranslate"` or `translate="no"`), translates the unique strings via your chosen backend, **caches them**, and writes them back, preserving all your markup. Cached strings never hit the API again.

The first visit to a page in a new language translates a batch of strings (configurable, default 40) and fills the rest on subsequent views, so no single request times out. Point it at a self-hosted LibreTranslate for fast bulk translation.

## Never translate

Some words should read the same in every language: brand names, product names, domain extensions like `.org`, code, and so on. Under **Settings → LangAddon → Never translate**, add one word or phrase per line. LangAddon keeps them exactly as written wherever they appear, even in the middle of a sentence, by shielding them before the text goes to the backend and restoring them afterward.

Turn on **Keep prices untranslated** to leave any text containing a currency amount (`$`, `€`, `£`, `₿`) alone, so "Only $49" never turns into "Only 49 €". You can also mark any element in your theme with `class="notranslate"` or `translate="no"` to skip it; the list and the markup work together.

## Backends

| Backend | Cost | Key needed | Notes |
|---|---|---|---|
| **MyMemory** | Free | No | Great for getting started; rate-limited (add an email in settings to raise the limit). |
| **LibreTranslate** | Free | Optional | Self-host for unlimited, private, fast translation. |
| **Google Cloud Translation** | Pay-as-you-go | Yes | ~500k chars/month free tier, then cheap. Highest coverage. |
| **DeepL** | Free tier + paid | Yes | Best quality for major European languages. |

## Routing & SEO

Translation is done server-side (the finished HTML is rewritten in PHP), so crawlers get fully translated pages, not JavaScript-injected text. Each variant routes with `?lang=xx` plus a cookie, outputs `hreflang` alternates, and is **self-canonical** (its `canonical` and `og:url` point at the translated URL), so search engines index each language rather than folding it back into the source. Pretty `/de/` sub-directory URLs, the pattern Google prefers, are the next SEO step on the roadmap.

## Roadmap

- [ ] Sub-directory routing (`/de/`, `/fr/`) with rewrite rules
- [ ] Front-end translation editor (click a string, fix it)
- [ ] "Warm" tool to pre-translate all pages from the admin
- [ ] WP-CLI command for bulk translation
- [ ] Per-post translate/exclude controls
- [x] Glossary / do-not-translate list (shipped in v1.1.0)

PRs welcome. See `CONTRIBUTING` once opened.

## Developer hooks

- `langaddon_settings` (option): all configuration.
- Filter the translated string per language by extending `Langaddon_Translator::provider_translate()` or adding a provider case.
- Mark any element `class="notranslate"` or `translate="no"` to skip it.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

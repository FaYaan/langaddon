# LangAddon: Free Website Translation for WordPress

Make any WordPress site multilingual **for free**. LangAddon machine-translates your pages into 40+ languages using **free, no-lock-in backends** (MyMemory or a self-hosted LibreTranslate, or your own Google/DeepL key), **caches every translation in your own database**, and lets you **edit any string**. Ships with a language switcher, right-to-left support and `hreflang` tags.

Built and open-sourced by [HostAddon](https://www.hostaddon.com) as part of a set of free tools for startups and small teams. MIT-spirited, GPL-licensed, no subscription.

## Why

Most WordPress translation plugins are either paid (TranslatePress, WPML, Weglot) or send every visitor through a third-party widget with no SEO and no way to fix a bad translation (GTranslate free). LangAddon aims for the honest middle: **free, self-hosted, cached, and editable.**

## Features

- 🌍 **40+ languages**, including RTL (Arabic, Farsi, Hebrew).
- 🆓 **No paid API required**: defaults to MyMemory (no key). Optional LibreTranslate (self-host), Google or DeepL.
- 💾 **Self-hosted cache**: each string is translated once and stored in your DB; repeat views are instant.
- ✍️ **Editable**: every cached translation lives in your database (a front-end editor is on the roadmap; for now edit rows directly or via the filter).
- 🔀 **Language switcher**: shortcode `[langaddon_switcher]`, a floating widget, or your theme's switcher slot.
- 🔎 **SEO basics**: `hreflang` alternate tags and translated `<title>` / meta description.
- ↔️ **RTL aware**: sets `dir="rtl"` and language attributes automatically.
- 🪶 **Lightweight**: one small stylesheet, no page builder, no external JS widget.

## Install

1. Download the latest release `.zip` (or clone this repo into `wp-content/plugins/langaddon`).
2. **Plugins → Add New → Upload Plugin**, activate.
3. **Settings → LangAddon**: choose your source language, tick the languages to translate into, pick a backend, save.
4. Add the switcher: drop `[langaddon_switcher]` into a menu/widget/template, or enable the floating switcher.

## How it works

On a non-default language, LangAddon buffers the rendered HTML, walks the visible text nodes (skipping `<script>`, `<style>`, `<code>`, and anything marked `class="notranslate"` or `translate="no"`), translates the unique strings via your chosen backend, **caches them**, and writes them back, preserving all your markup. Cached strings never hit the API again.

The first visit to a page in a new language translates a batch of strings (configurable, default 40) and fills the rest on subsequent views, so no single request times out. Point it at a self-hosted LibreTranslate for fast bulk translation.

## Backends

| Backend | Cost | Key needed | Notes |
|---|---|---|---|
| **MyMemory** | Free | No | Great for getting started; rate-limited (add an email in settings to raise the limit). |
| **LibreTranslate** | Free | Optional | Self-host for unlimited, private, fast translation. |
| **Google Cloud Translation** | Pay-as-you-go | Yes | ~500k chars/month free tier, then cheap. Highest coverage. |
| **DeepL** | Free tier + paid | Yes | Best quality for major European languages. |

## Routing & SEO

v1 routes with `?lang=xx` + a cookie, and outputs `hreflang` alternates so search engines can discover each language. Pretty `/de/` sub-directory URLs (stronger SEO) are on the roadmap.

## Roadmap

- [ ] Sub-directory routing (`/de/`, `/fr/`) with rewrite rules
- [ ] Front-end translation editor (click a string, fix it)
- [ ] "Warm" tool to pre-translate all pages from the admin
- [ ] WP-CLI command for bulk translation
- [ ] Per-post translate/exclude controls
- [ ] Glossary / do-not-translate list

PRs welcome. See `CONTRIBUTING` once opened.

## Developer hooks

- `langaddon_settings` (option): all configuration.
- Filter the translated string per language by extending `Langaddon_Translator::provider_translate()` or adding a provider case.
- Mark any element `class="notranslate"` or `translate="no"` to skip it.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

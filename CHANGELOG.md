# Changelog

All notable changes to LangAddon are documented here. LangAddon is free for anyone to use, fork, and build on. This project follows [Semantic Versioning](https://semver.org/).

## [1.1.7] - 2026-08-16

### Fixed
- The target-language checkboxes now emit their `checked` state inline (like the dropdown's `selected()`), clearing the final Plugin Check output-escaping error. No functional changes.

## [1.1.6] - 2026-08-16

### Fixed
- Escaped a settings-screen label (the RTL indicator) flagged by Plugin Check's output-escaping check. No functional changes.

## [1.1.5] - 2026-08-15

### Fixed
- The language switcher can now return to the source language. Selecting it used to leave the previously chosen language's cookie in place, so the page stayed translated.

### Improved
- On translated pages, the Open Graph `og:locale` is now localised (for example `de_DE`, `tr_TR`, `ar_AR`) so social-share previews match the page language.

## [1.1.4] - 2026-08-15

### Added
- Screenshots section in the readme for the WordPress.org listing. No code changes.

## [1.1.3] - 2026-08-15

### Changed
- Coding-standards hardening for the WordPress.org plugin review: escaped all admin settings-screen output, guarded and prepared the database calls on the plugin's cache table, sanitized the `lang` query variable before use, removed the deprecated `load_plugin_textdomain()` call (WordPress auto-loads translations since 4.6), and trimmed the readme tags and short description. No functional changes.

## [1.1.2] - 2026-08-15

### Fixed
- SEO strings (the page title, meta description, and Open Graph title/description) now get a dedicated translation pass on the first view, independent of the per-page string budget. Previously they queued behind body text and could take many views to translate on content-heavy pages.

## [1.1.1] - 2026-08-15

### Fixed
- SEO: translated pages now emit a self-referencing canonical URL and `og:url`, so search engines index the translated variant instead of canonicalizing it back to the source language. This makes non-source languages indexable and rankable.

## [1.1.0] - 2026-08-15

### Added
- **Never translate** list: keep chosen words and phrases identical in every language, wherever they appear, including inside sentences. Set it under Settings → LangAddon → Never translate, one term per line. Useful for brand names, product names, domain extensions like `.org`, and code.
- Optional **price guard**: leave any text containing a currency amount (`$`, `€`, `£`, `₿`) untranslated.

## [1.0.0] - 2026-08-15

### Added
- Initial release: free machine translation into 40+ languages via MyMemory, LibreTranslate, Google, or DeepL.
- Self-hosted cache with editable translations stored in your own database, so each string is translated once and repeat views are instant.
- Language switcher shortcode `[langaddon_switcher]`, floating widget, RTL support, and `hreflang` tags.

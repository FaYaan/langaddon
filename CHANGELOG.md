# Changelog

All notable changes to LangAddon are documented here. LangAddon is free for anyone to use, fork, and build on. This project follows [Semantic Versioning](https://semver.org/).

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

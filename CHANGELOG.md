# Changelog

All notable changes to OT Hello are documented here.

## 0.1.2 — 2026-08-23

### Changed

- OT convention: install folder, text domain, and ZIP root are `ot-hello`; bootstrap `ot-hello.php`; PHP namespace `OTHello\`; REST `ot-hello/v1`
- License prefix `OT-HELLO-` (legacy `OT-HW-` still accepted)
- Options `ot_hello_settings` / `ot_hello_license` (legacy `ot_hello_world_*` read and migrated on save)
- Panic switch `OT_HELLO_DISABLE` (legacy `OT_HELLO_WORLD_DISABLE` still honored)
- Shortcode `[ot_hello]` (legacy `[ot_hello_world]` still registered)
- Canonical local tree: `C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird\ot-hello`
- `deploy-update` reads sibling OneThird FTP env (`../ai-rewriter/scripts/.env`) and never reuses rewriter’s remote dir

## 0.1.1 — 2026-08-22

### Changed

- Product display name is **OT Hello** (install slug, REST, and text domain stay `ot-hello-world`)
- GitHub: https://github.com/hikikomorime/ot-hello
- Update catalog matches OT re:Writer: `https://plugins.onethird.pl/updates/plugins/ot-hello/` (`update.json`)

## 0.1.0 — 2026-08-21

### Added

- Fluent admin hub: Dashboard, Settings, Help, About
- Greeting stored in `ot_hello_world_settings` (Hello / World / site-info toggle)
- REST namespace `ot-hello-world/v1` (`status`, `greeting`, `license`)
- Front-end shortcode `[ot_hello_world]`
- License key `OT-HW-…` for official ZIP updates only
- Updater stub against `plugins.onethird.pl/updates/plugins/ot-hello-world/`
- Panic switch `OT_HELLO_WORLD_DISABLE`
- English source strings and `pl_PL` catalog
- `archive-release` / `deploy-update` matching the shared OT FTP layout

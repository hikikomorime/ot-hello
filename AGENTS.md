# AGENTS.md — OT Hello

OneThird WordPress plugin. Match this file over generic OT defaults when they conflict.

## Identity

- Product / GitHub: OT Hello — https://github.com/hikikomorime/ot-hello
- Local tree: `C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird\ot-hello`
- Update catalog: `ot-hello` → `https://plugins.onethird.pl/updates/plugins/ot-hello/`
- Slug / text domain / install folder: `ot-hello`
- Bootstrap: `ot-hello.php`
- PHP namespace: `OTHello\`
- REST: `ot-hello/v1`
- License prefix: `OT-HELLO-` (legacy `OT-HW-` accepted)
- License model: **license = updates** (do not feature-gate the greeting)
- Options: `ot_hello_settings`, `ot_hello_license` (legacy `ot_hello_world_*` migrated on save)
- Panic: `OT_HELLO_DISABLE` (legacy `OT_HELLO_WORLD_DISABLE` still honored)
- PHP 8.1+, WordPress 6.4+

## Layout

- PSR-4: `src/` + `includes/autoload.php` (Composer optional on the site)
- React Fluent hub: `admin/src/` → `build/admin.js` + `build/admin.css` (Vite)
- Release ZIP dir: `backups/` (`ot-hello-vX.Y.Z.zip`, root folder `ot-hello/`)
- CallHome is infrastructure only — never a product tab
- FTP: `scripts/.env` or sibling `../ai-rewriter/scripts/.env` — never commit secrets

## Verify before claiming done

```bash
npm run verify-php
composer exec phpunit
npm run typecheck
npm run build
npm run i18n
npm run archive-release
```

Or: `npm run pre-release` (needs `composer install` first).

## Admin IA

Dashboard / Settings / Help / About. Fluent light tokens `--ot-hello-*`. English source strings; ship `pl_PL`.

## Version bump

1. `ot-hello.php`, `package.json`, `readme.txt`, `CHANGELOG.md`
2. `npm run archive-release`
3. Commit `backups/ot-hello-vX.Y.Z.zip` (do not gitignore `backups/`)
4. `npm run deploy-update` (sibling rewriter `.env` is enough)
5. `git push github main`
6. Conventional commit (`feat`, `fix`, `chore`, …)

Never commit `scripts/.env`.

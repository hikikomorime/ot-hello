# OT Hello

Nowoczesna wtyczka WordPress **OT Hello** z linii OneThird: jasny panel Fluent (Pulpit / Ustawienia / Pomoc / O wtyczce), REST API, shortcode i oficjalne aktualizacje ZIP z `plugins.onethird.pl`.

Źródło: [github.com/hikikomorime/ot-hello](https://github.com/hikikomorime/ot-hello).

## Katalog OneThird (Windows)

Kanoniczne miejsce w drzewie projektów:

```
C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird\ot-hello
```

Obok `ot-seo`, `ai-rewriter` / `ot-rewriter`. Skrypt `scripts/bootstrap-onethird.ps1` klonuje repo do tego folderu i podłącza wspólne konto FTP z siblinga.

```powershell
cd C:\Users\micha\OneDrive\Dokumenty\AI-Projects\OneThird
git clone https://github.com/hikikomorime/ot-hello.git ot-hello
cd ot-hello
powershell -File scripts\bootstrap-onethird.ps1
```

## Wymagania

- WordPress 6.4+
- PHP 8.1+
- Node 20+ (tylko do budowania panelu i ZIP-a)

## Instalacja w WordPressie

1. Zbuduj wydanie: `npm install && composer install && npm run archive-release`
2. Wgraj `backups/ot-hello-v0.1.2.zip` w **Wtyczki → Dodaj nową → Wyślij wtyczkę**
3. Włącz **OT Hello** i otwórz pozycję menu **Hello**

Albo skopiuj katalog `ot-hello/` (z plikiem `ot-hello.php` w korzeniu) do `wp-content/plugins/`.

Jeśli wcześniej stała wersja `ot-hello-world`, wyłącz ją i usuń — 0.1.2 to nowy folder instalacji. Ustawienia i klucz z opcji `ot_hello_world_*` są czytane i przenoszone przy zapisie.

## Podgląd panelu bez WordPressa

```bash
npm install
npm run dev
```

Panel Fluent stoi na [http://127.0.0.1:43217](http://127.0.0.1:43217). Tryb podglądu trzyma powitanie i licencję w `localStorage`.

## Tożsamość

| Pole | Wartość |
|------|---------|
| Nazwa | OT Hello |
| Folder / text domain | `ot-hello` |
| Namespace PHP | `OTHello\` |
| REST | `ot-hello/v1` |
| Prefiks klucza | `OT-HELLO-` (stary `OT-HW-` nadal działa) |
| Model licencji | license = updates (pełne funkcje bez klucza) |
| GitHub | https://github.com/hikikomorime/ot-hello |
| Katalog aktualizacji | `ot-hello` |
| Update URI | `https://plugins.onethird.pl/updates/plugins/ot-hello/` |
| Lokalne drzewo | `…\AI-Projects\OneThird\ot-hello` |

## Panic switch

W `wp-config.php`:

```php
define( 'OT_HELLO_DISABLE', true );
```

Stary `OT_HELLO_WORLD_DISABLE` jest nadal honorowany.

## Weryfikacja

```bash
composer install
npm install
npm run pre-release
```

Skrypt `pre-release` odpala `php -l`, PHPUnit, TypeScript, build Vite, kontrolę i18n i ZIP do `backups/`.

## Aktualizacje FTP

To samo konto FTP co OT re:Writer / ot-seo. `npm run deploy-update` czyta `scripts/.env`, a jeśli go nie ma — `../ai-rewriter/scripts/.env` (albo `../ot-rewriter`, `../ot-seo`). Katalog na serwerze zawsze: `/updates/plugins/ot-hello/`. Szczegóły: [docs/PLUGIN-UPDATES.md](docs/PLUGIN-UPDATES.md).

## Shortcode

```
[ot_hello]
```

Stary `[ot_hello_world]` nadal działa.

## Licencja

GPL v2 or later. Autor: Michael Skweres | OneThird — https://onethird.pl

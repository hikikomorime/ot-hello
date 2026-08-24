# OT Hello — przewodnik

## Co to jest

Mała, nowoczesna wtyczka OneThird. Pokazuje powitanie *Hello, World* w panelu WordPressa i na froncie, bez bramkowania funkcji kluczem licencyjnym.

## Pierwsze kroki

1. Włącz wtyczkę **OT Hello**.
2. W menu admina otwórz **Hello**.
3. Na **Pulpicie** zobaczysz złożone powitanie (domyślnie `Hello, World!`).
4. W **Ustawieniach** zmień powitanie i adresata, potem **Zapisz zmiany**.
5. W treści strony wstaw `[ot_hello]`.

## Licencja

Wtyczka działa w całości bez klucza. Klucz w formacie `OT-HELLO-XXXX-XXXX-XXXX-XXXX` (ekran **O wtyczce**) włącza tylko oficjalne pakiety ZIP z `https://plugins.onethird.pl/updates/plugins/ot-hello/`. Stary format `OT-HW-…` nadal działa.

Kod: https://github.com/hikikomorime/ot-hello

## Awaryjne wyłączenie

W `wp-config.php`:

```php
define( 'OT_HELLO_DISABLE', true );
```

## REST (administrator)

- `GET /wp-json/ot-hello/v1/status`
- `GET|POST /wp-json/ot-hello/v1/greeting`
- `GET|POST /wp-json/ot-hello/v1/license`

Żądania wymagają uprawnienia `manage_options` i nonce REST WordPressa.

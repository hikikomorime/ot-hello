=== OT Hello ===
Contributors: onethird, mskweres
Tags: hello-world, onethird, admin, rest-api
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 8.1
Stable tag: 0.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern OneThird hello plugin: Fluent admin hub, REST API, shortcode greeting, and official updates.

== Description ==

OT Hello is the greenfield template for the OneThird WordPress plugin line.

* Fluent light admin hub: Dashboard, Settings, Help, About
* Customizable "Hello, World" greeting stored in `ot_hello_settings`
* REST namespace `ot-hello/v1`
* Front-end shortcode `[ot_hello]`
* License key `OT-HELLO-…` unlocks official ZIP updates only — every feature works without a key
* Updates from `https://plugins.onethird.pl/updates/plugins/ot-hello/`
* Source: https://github.com/hikikomorime/ot-hello

English is the source language. A `pl_PL` translation ships in `/languages`.

== Installation ==

1. Upload the `ot-hello` folder to `/wp-content/plugins/`.
2. Activate **OT Hello**.
3. Open **Hello** in the WordPress admin menu.

== Frequently Asked Questions ==

= Does it need a license key? =

No. All features work without a key. A valid `OT-HELLO-` key enables official ZIP updates from plugins.onethird.pl.

= How do I disable the plugin in an emergency? =

Add `define( 'OT_HELLO_DISABLE', true );` to `wp-config.php`.

== Changelog ==

= 0.1.2 =
* OT convention: install slug ot-hello, PHP OTHello, REST ot-hello/v1.

= 0.1.1 =
* Product display name is OT Hello.

= 0.1.0 =
* First public slice: Fluent hub, greeting settings, REST, shortcode, updater stub.

== Upgrade Notice ==

= 0.1.2 =
Install folder is now ot-hello. Deactivate the old ot-hello-world copy first. Settings and license migrate automatically.

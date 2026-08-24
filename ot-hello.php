<?php
/**
 * Plugin Name:       OT Hello
 * Plugin URI:        https://onethird.pl
 * Description:       A modern OneThird hello plugin: Fluent admin hub, REST API, shortcode greeting, and official updates via plugins.onethird.pl.
 * Version:           0.1.2
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Michael Skweres | OneThird
 * Author URI:        https://onethird.pl
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ot-hello
 * Domain Path:       /languages
 * Update URI:        https://plugins.onethird.pl/updates/plugins/ot-hello/
 *
 * @package OTHello
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('OT_HELLO_VERSION', '0.1.2');
define('OT_HELLO_FILE', __FILE__);
define('OT_HELLO_PATH', plugin_dir_path(__FILE__));
define('OT_HELLO_URL', plugin_dir_url(__FILE__));
define('OT_HELLO_SLUG', 'ot-hello');
define('OT_HELLO_REST_NAMESPACE', 'ot-hello/v1');

$ot_hello_vendor = OT_HELLO_PATH . 'vendor/autoload.php';
if (is_readable($ot_hello_vendor)) {
    require_once $ot_hello_vendor;
} else {
    require_once OT_HELLO_PATH . 'includes/autoload.php';
}

register_activation_hook(__FILE__, static function (): void {
    \OTHello\Activator::activate();
});

register_deactivation_hook(__FILE__, static function (): void {
    \OTHello\Deactivator::deactivate();
});

add_action('plugins_loaded', static function (): void {
    $disabled = (defined('OT_HELLO_DISABLE') && OT_HELLO_DISABLE)
        || (defined('OT_HELLO_WORLD_DISABLE') && OT_HELLO_WORLD_DISABLE);
    if ($disabled) {
        return;
    }

    \OTHello\Plugin::instance()->boot();
});

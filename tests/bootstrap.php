<?php
/**
 * PHPUnit bootstrap with WordPress function stubs.
 *
 * @package OTHello
 */

declare(strict_types=1);

$vendor = dirname(__DIR__) . '/vendor/autoload.php';
if (! is_readable($vendor)) {
    fwrite(STDERR, "Run composer install before phpunit.\n");
    exit(1);
}

require_once $vendor;

if (! defined('ABSPATH')) {
    define('ABSPATH', '/tmp/ot-hello-wp/');
}
if (! defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}
if (! defined('OT_HELLO_VERSION')) {
    define('OT_HELLO_VERSION', '0.1.2');
}
if (! defined('OT_HELLO_FILE')) {
    define('OT_HELLO_FILE', dirname(__DIR__) . '/ot-hello.php');
}
if (! defined('OT_HELLO_PATH')) {
    define('OT_HELLO_PATH', dirname(__DIR__) . '/');
}
if (! defined('OT_HELLO_URL')) {
    define('OT_HELLO_URL', 'https://example.test/wp-content/plugins/ot-hello/');
}
if (! defined('OT_HELLO_SLUG')) {
    define('OT_HELLO_SLUG', 'ot-hello');
}
if (! defined('OT_HELLO_REST_NAMESPACE')) {
    define('OT_HELLO_REST_NAMESPACE', 'ot-hello/v1');
}

$GLOBALS['ot_hello_options'] = [];
$GLOBALS['ot_hello_transients'] = [];

if (! function_exists('get_option')) {
    function get_option(string $key, mixed $default = false): mixed
    {
        return $GLOBALS['ot_hello_options'][$key] ?? $default;
    }
}

if (! function_exists('update_option')) {
    function update_option(string $key, mixed $value, mixed $autoload = true): bool
    {
        $GLOBALS['ot_hello_options'][$key] = $value;

        return true;
    }
}

if (! function_exists('delete_option')) {
    function delete_option(string $key): bool
    {
        unset($GLOBALS['ot_hello_options'][$key]);

        return true;
    }
}

if (! function_exists('sanitize_text_field')) {
    function sanitize_text_field(string $str): string
    {
        $stripped = preg_replace('/[\r\n\t\0\x0B]+/', '', strip_tags($str));

        return trim(is_string($stripped) ? $stripped : '');
    }
}

if (! function_exists('get_transient')) {
    function get_transient(string $key): mixed
    {
        return $GLOBALS['ot_hello_transients'][$key] ?? false;
    }
}

if (! function_exists('set_transient')) {
    function set_transient(string $key, mixed $value, int $ttl = 0): bool
    {
        $GLOBALS['ot_hello_transients'][$key] = $value;

        return true;
    }
}

if (! function_exists('delete_transient')) {
    function delete_transient(string $key): bool
    {
        unset($GLOBALS['ot_hello_transients'][$key]);

        return true;
    }
}

if (! function_exists('delete_site_transient')) {
    function delete_site_transient(string $key): bool
    {
        unset($GLOBALS['ot_hello_transients'][$key]);

        return true;
    }
}

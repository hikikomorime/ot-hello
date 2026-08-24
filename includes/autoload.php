<?php
/**
 * Lightweight PSR-4 fallback when Composer vendor/ is not installed.
 *
 * @package OTHello
 */

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'OTHello\\';
    $base_dir = dirname(__DIR__) . '/src/';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (is_readable($file)) {
        require_once $file;
    }
});

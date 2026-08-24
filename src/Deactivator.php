<?php
/**
 * Deactivation hook — settings are kept until uninstall.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello;

final class Deactivator
{
    public static function deactivate(): void
    {
        delete_transient('ot_hello_update');
        delete_transient('ot_hello_world_update');
    }
}

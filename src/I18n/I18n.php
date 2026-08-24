<?php
/**
 * Loads the plugin text domain.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\I18n;

final class I18n
{
    public function register(): void
    {
        load_plugin_textdomain(
            'ot-hello',
            false,
            dirname(plugin_basename(OT_HELLO_FILE)) . '/languages'
        );
    }
}

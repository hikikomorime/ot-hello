<?php
/**
 * Activation defaults.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello;

use OTHello\Settings\SettingsRepository;

final class Activator
{
    public static function activate(): void
    {
        $settings = new SettingsRepository();
        $raw = $settings->getRaw();
        if ($raw === false) {
            $settings->save($settings->defaults());

            return;
        }

        if (is_array($raw) && get_option(SettingsRepository::OPTION_KEY, false) === false) {
            $settings->save($raw);
        }
    }
}

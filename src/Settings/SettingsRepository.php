<?php
/**
 * Greeting and display settings stored in a single option.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Settings;

final class SettingsRepository
{
    public const OPTION_KEY = 'ot_hello_settings';
    public const LEGACY_OPTION_KEY = 'ot_hello_world_settings';

    /**
     * @return array{greeting: string, audience: string, show_site_info: bool}
     */
    public function defaults(): array
    {
        return [
            'greeting' => 'Hello',
            'audience' => 'World',
            'show_site_info' => true,
        ];
    }

    /**
     * @return array{greeting: string, audience: string, show_site_info: bool}
     */
    public function get(): array
    {
        $stored = $this->getRaw();
        if (! is_array($stored)) {
            return $this->defaults();
        }

        return $this->sanitize($stored);
    }

    public function getRaw(): mixed
    {
        $stored = get_option(self::OPTION_KEY, false);
        if ($stored !== false) {
            return $stored;
        }

        return get_option(self::LEGACY_OPTION_KEY, false);
    }

    /**
     * @param array<string, mixed> $input
     * @return array{greeting: string, audience: string, show_site_info: bool}
     */
    public function save(array $input): array
    {
        $clean = $this->sanitize($input);
        update_option(self::OPTION_KEY, $clean, false);
        delete_option(self::LEGACY_OPTION_KEY);

        return $clean;
    }

    public function composeMessage(): string
    {
        $settings = $this->get();
        $greeting = trim($settings['greeting']);
        $audience = trim($settings['audience']);

        if ($greeting === '' && $audience === '') {
            return '';
        }

        $greeting = $greeting !== '' ? $greeting : 'Hello';
        $audience = $audience !== '' ? $audience : 'World';

        return sprintf('%s, %s!', $greeting, $audience);
    }

    /**
     * @param array<string, mixed> $input
     * @return array{greeting: string, audience: string, show_site_info: bool}
     */
    public function sanitize(array $input): array
    {
        $defaults = $this->defaults();

        $greeting = isset($input['greeting']) ? sanitize_text_field((string) $input['greeting']) : $defaults['greeting'];
        $audience = isset($input['audience']) ? sanitize_text_field((string) $input['audience']) : $defaults['audience'];

        if (strlen($greeting) > 80) {
            $greeting = substr($greeting, 0, 80);
        }
        if (strlen($audience) > 80) {
            $audience = substr($audience, 0, 80);
        }

        $show = $defaults['show_site_info'];
        if (array_key_exists('show_site_info', $input)) {
            $show = $this->toBool($input['show_site_info']);
        }

        return [
            'greeting' => $greeting,
            'audience' => $audience,
            'show_site_info' => $show,
        ];
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }
}

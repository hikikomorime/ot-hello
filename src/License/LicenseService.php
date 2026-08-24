<?php
/**
 * License key storage. Features stay unlocked; a valid key enables official ZIP updates.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\License;

final class LicenseService
{
    public const OPTION_KEY = 'ot_hello_license';
    public const LEGACY_OPTION_KEY = 'ot_hello_world_license';
    public const PREFIX = 'OT-HELLO-';
    public const LEGACY_PREFIX = 'OT-HW-';

    /**
     * @return array{key: string, status: 'missing'|'invalid'|'saved'}
     */
    public function getState(): array
    {
        $key = $this->getKey();
        if ($key === '') {
            return ['key' => '', 'status' => 'missing'];
        }

        return [
            'key' => $this->mask($key),
            'status' => $this->isValidFormat($key) ? 'saved' : 'invalid',
        ];
    }

    public function getKey(): string
    {
        $stored = get_option(self::OPTION_KEY, '');
        if (is_string($stored) && trim($stored) !== '') {
            return trim($stored);
        }

        $legacy = get_option(self::LEGACY_OPTION_KEY, '');

        return is_string($legacy) ? trim($legacy) : '';
    }

    /**
     * @return array{key: string, status: 'missing'|'invalid'|'saved'}
     */
    public function save(string $key, bool $clear = false): array
    {
        $key = strtoupper(trim(sanitize_text_field($key)));
        if (strlen($key) > 64) {
            $key = substr($key, 0, 64);
        }

        if ($key === '') {
            if ($clear) {
                delete_option(self::OPTION_KEY);
                delete_option(self::LEGACY_OPTION_KEY);
            }

            return $this->getState();
        }

        update_option(self::OPTION_KEY, $key, false);
        delete_option(self::LEGACY_OPTION_KEY);

        if ($this->isValidFormat($key)) {
            delete_transient('ot_hello_update');
            delete_transient('ot_hello_world_update');
            if (function_exists('delete_site_transient')) {
                delete_site_transient('update_plugins');
            }
        }

        return $this->getState();
    }

    public function isValidFormat(string $key): bool
    {
        $key = strtoupper(trim($key));

        return (bool) preg_match('/^OT-HELLO-[A-Z0-9]{4}(?:-[A-Z0-9]{4}){3}$/', $key)
            || (bool) preg_match('/^OT-HW-[A-Z0-9]{4}(?:-[A-Z0-9]{4}){3}$/', $key);
    }

    public function canReceiveOfficialUpdates(): bool
    {
        return $this->isValidFormat($this->getKey());
    }

    public function mask(string $key): string
    {
        $key = strtoupper(trim($key));
        if (strlen($key) < 10) {
            return $key;
        }

        $keep = str_starts_with($key, self::PREFIX) ? 9 : 6;

        return substr($key, 0, $keep) . str_repeat('•', max(0, strlen($key) - $keep - 4)) . substr($key, -4);
    }
}

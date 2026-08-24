<?php
/**
 * OneThird plugin server host and update URLs (single source of truth).
 *
 * Matches OT re:Writer: product catalog slug on plugins.onethird.pl,
 * not the WordPress install folder.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Updates;

final class OneThirdPluginsConfig
{
    public const HOST = 'plugins.onethird.pl';
    public const SCHEME = 'https';
    public const UPDATE_PLUGIN_SLUG = 'ot-hello';
    public const UPDATE_PATH_PREFIX = '/updates/plugins/ot-hello/';
    public const GITHUB_URL = 'https://github.com/hikikomorime/ot-hello';

    public static function origin(): string
    {
        return self::SCHEME . '://' . self::HOST;
    }

    public static function updateBaseUrl(): string
    {
        return self::origin() . self::UPDATE_PATH_PREFIX;
    }

    public static function updateManifestUrl(): string
    {
        return self::updateBaseUrl() . 'update.json';
    }

    public static function latestManifestUrl(): string
    {
        return self::updateBaseUrl() . 'latest.json';
    }

    public static function versionTxtUrl(): string
    {
        return self::updateBaseUrl() . 'version.txt';
    }

    public static function isAllowedHost(string $host): bool
    {
        return strtolower($host) === self::HOST;
    }
}

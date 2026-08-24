<?php
/**
 * Official ZIP updates from plugins.onethird.pl.
 * A valid OT-HELLO (or legacy OT-HW) license key is required to receive update payloads.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Updates;

use OTHello\License\LicenseService;

final class PluginUpdater
{

    public function __construct(private readonly LicenseService $license)
    {
    }

    public function register(): void
    {
        add_filter('pre_set_site_transient_update_plugins', [$this, 'injectUpdate']);
        add_filter('plugins_api', [$this, 'pluginsApi'], 10, 3);
        add_filter('http_request_args', [$this, 'identifyRequest'], 10, 2);
    }

    /**
     * @param mixed $transient
     * @return mixed
     */
    public function injectUpdate(mixed $transient): mixed
    {
        if (! is_object($transient) || empty($transient->checked)) {
            return $transient;
        }

        $manifest = $this->fetchManifest();
        if ($manifest === null) {
            return $transient;
        }

        $plugin_basename = plugin_basename(OT_HELLO_FILE);
        $remote_version = $manifest['version'] ?? '';
        if (! is_string($remote_version) || $remote_version === '') {
            return $transient;
        }

        if (version_compare(OT_HELLO_VERSION, $remote_version, '>=')) {
            return $transient;
        }

        if (! $this->license->canReceiveOfficialUpdates()) {
            return $transient;
        }

        $package = $this->trustedPackageUrl($manifest['download_url'] ?? null);
        if ($package === '') {
            return $transient;
        }

        $transient->response[$plugin_basename] = (object) [
            'slug' => OT_HELLO_SLUG,
            'plugin' => $plugin_basename,
            'new_version' => $remote_version,
            'url' => 'https://onethird.pl',
            'package' => $package,
            'tested' => is_string($manifest['tested'] ?? null) ? $manifest['tested'] : '',
            'requires' => is_string($manifest['requires'] ?? null) ? $manifest['requires'] : '6.4',
            'requires_php' => is_string($manifest['requires_php'] ?? null) ? $manifest['requires_php'] : '8.1',
        ];

        return $transient;
    }

    /**
     * @param mixed  $result
     * @param string $action
     * @param mixed  $args
     * @return mixed
     */
    public function pluginsApi(mixed $result, string $action, mixed $args): mixed
    {
        if ($action !== 'plugin_information' || ! is_object($args) || ($args->slug ?? '') !== OT_HELLO_SLUG) {
            return $result;
        }

        $manifest = $this->fetchManifest();
        if ($manifest === null) {
            return $result;
        }

        return (object) [
            'name' => 'OT Hello',
            'slug' => OT_HELLO_SLUG,
            'version' => $manifest['version'] ?? OT_HELLO_VERSION,
            'author' => '<a href="https://onethird.pl">Michael Skweres | OneThird</a>',
            'homepage' => 'https://onethird.pl',
            'requires' => $manifest['requires'] ?? '6.4',
            'tested' => $manifest['tested'] ?? '',
            'requires_php' => $manifest['requires_php'] ?? '8.1',
            'download_link' => $this->trustedPackageUrl(
                $this->license->canReceiveOfficialUpdates() ? ($manifest['download_url'] ?? null) : null
            ),
            'sections' => [
                'description' => $manifest['description'] ?? 'A modern OneThird hello plugin.',
                'changelog' => $manifest['changelog'] ?? '',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>
     */
    public function identifyRequest(array $args, string $url): array
    {
        if (! str_contains($url, 'plugins.onethird.pl' . OneThirdPluginsConfig::UPDATE_PATH_PREFIX)) {
            return $args;
        }

        $args['user-agent'] = 'OT-Hello/' . OT_HELLO_VERSION . '; ' . home_url('/');
        $headers = is_array($args['headers'] ?? null) ? $args['headers'] : [];
        $headers['X-OT-License'] = $this->license->canReceiveOfficialUpdates() ? '1' : '0';
        $args['headers'] = $headers;

        return $args;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchManifest(): ?array
    {
        $cached = get_transient('ot_hello_update');
        if (is_array($cached)) {
            return $cached;
        }

        $args = [
            'timeout' => 8,
            'redirection' => 0,
            'sslverify' => true,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        foreach ([OneThirdPluginsConfig::updateManifestUrl(), OneThirdPluginsConfig::latestManifestUrl()] as $url) {
            $clean = $this->fetchManifestFromUrl($url, $args);
            if ($clean !== null) {
                set_transient('ot_hello_update', $clean, 6 * HOUR_IN_SECONDS);

                return $clean;
            }
        }

        $fallback = $this->fetchVersionTxtFallback($args);
        if ($fallback !== null) {
            set_transient('ot_hello_update', $fallback, 6 * HOUR_IN_SECONDS);
        }

        return $fallback;
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>|null
     */
    private function fetchManifestFromUrl(string $url, array $args): ?array
    {
        $response = wp_remote_get($url, $args);
        if (is_wp_error($response)) {
            return null;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (! is_array($data)) {
            return null;
        }

        return $this->sanitizeManifest($data);
    }

    /**
     * @param array<string, mixed> $args
     * @return array<string, mixed>|null
     */
    private function fetchVersionTxtFallback(array $args): ?array
    {
        $response = wp_remote_get(OneThirdPluginsConfig::versionTxtUrl(), $args);
        if (is_wp_error($response)) {
            return null;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            return null;
        }

        $version = sanitize_text_field(trim(wp_remote_retrieve_body($response)));
        if ($version === '' || ! preg_match('/^[0-9]+(?:\.[0-9A-Za-z\-]+)*$/', $version)) {
            return null;
        }

        return $this->sanitizeManifest([
            'version' => $version,
            'download_url' => OneThirdPluginsConfig::updateBaseUrl() . 'ot-hello-v' . $version . '.zip',
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    private function sanitizeManifest(array $data): ?array
    {
        $version = isset($data['version']) && is_string($data['version']) ? $data['version'] : '';
        if ($version === '') {
            return null;
        }

        $download = $this->trustedPackageUrl($data['download_url'] ?? null);

        return [
            'version' => sanitize_text_field($version),
            'download_url' => $download,
            'tested' => isset($data['tested']) ? sanitize_text_field((string) $data['tested']) : '',
            'requires' => isset($data['requires']) ? sanitize_text_field((string) $data['requires']) : '6.4',
            'requires_php' => isset($data['requires_php']) ? sanitize_text_field((string) $data['requires_php']) : '8.1',
            'description' => isset($data['description']) ? sanitize_text_field((string) $data['description']) : '',
            'changelog' => isset($data['changelog'])
                ? (function_exists('wp_kses_post')
                    ? wp_kses_post((string) $data['changelog'])
                    : sanitize_text_field((string) $data['changelog']))
                : '',
        ];
    }

    private function trustedPackageUrl(mixed $url): string
    {
        if (! is_string($url) || $url === '') {
            return '';
        }

        $parts = function_exists('wp_parse_url') ? wp_parse_url($url) : parse_url($url);
        if (! is_array($parts)) {
            return '';
        }

        if (($parts['scheme'] ?? '') !== 'https') {
            return '';
        }

        if (! OneThirdPluginsConfig::isAllowedHost((string) ($parts['host'] ?? ''))) {
            return '';
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            return '';
        }

        if (isset($parts['port']) && (int) $parts['port'] !== 443) {
            return '';
        }

        $path = $parts['path'] ?? '';
        if (! is_string($path) || preg_match('/%2e|\.\./i', $path) === 1) {
            return '';
        }

        if (! preg_match('#^/updates/plugins/ot-hello/[A-Za-z0-9._-]+\.zip$#', $path)) {
            return '';
        }

        return $url;
    }
}

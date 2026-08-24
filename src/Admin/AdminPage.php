<?php
/**
 * WordPress admin page that mounts the Fluent React hub.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Admin;

use OTHello\License\LicenseService;
use OTHello\Settings\SettingsRepository;
use OTHello\Updates\OneThirdPluginsConfig;

final class AdminPage
{
    public function __construct(
        private readonly SettingsRepository $settings,
        private readonly LicenseService $license
    ) {
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function addMenu(): void
    {
        add_menu_page(
            __('OT Hello', 'ot-hello'),
            __('Hello', 'ot-hello'),
            'manage_options',
            'ot-hello',
            [$this, 'render'],
            'dashicons-smiley',
            58
        );
    }

    public function render(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'ot-hello'));
        }

        echo '<div class="wrap ot-hello-admin-wrap">';
        echo '<h1 class="screen-reader-text">' . esc_html__('OT Hello', 'ot-hello') . '</h1>';
        echo '<div id="ot-hello-root"></div>';
        echo '</div>';
    }

    public function enqueue(string $hook): void
    {
        if ($hook !== 'toplevel_page_ot-hello' || ! current_user_can('manage_options')) {
            return;
        }

        $script = OT_HELLO_PATH . 'build/admin.js';
        $style = OT_HELLO_PATH . 'build/admin.css';
        if (! is_readable($script)) {
            return;
        }

        $script_ver = (string) filemtime($script);
        $style_ver = is_readable($style) ? (string) filemtime($style) : OT_HELLO_VERSION;

        if (is_readable($style)) {
            wp_enqueue_style(
                'ot-hello-admin',
                OT_HELLO_URL . 'build/admin.css',
                [],
                $style_ver
            );
        }

        wp_enqueue_script(
            'ot-hello-admin',
            OT_HELLO_URL . 'build/admin.js',
            [],
            $script_ver,
            true
        );

        $site_name = function_exists('get_bloginfo') ? (string) get_bloginfo('name') : '';
        $wp_version = function_exists('get_bloginfo') ? (string) get_bloginfo('version') : '';

        wp_localize_script('ot-hello-admin', 'otHello', [
            'restUrl' => esc_url_raw(rest_url(OT_HELLO_REST_NAMESPACE . '/')),
            'nonce' => wp_create_nonce('wp_rest'),
            'version' => OT_HELLO_VERSION,
            'locale' => function_exists('determine_locale') ? determine_locale() : 'en_US',
            'preview' => false,
            'siteName' => $site_name,
            'wpVersion' => $wp_version,
            'phpVersion' => PHP_VERSION,
            'pluginUrl' => 'https://onethird.pl',
            'docsUrl' => 'https://onethird.pl',
            'updateUri' => OneThirdPluginsConfig::updateBaseUrl(),
            'githubUrl' => OneThirdPluginsConfig::GITHUB_URL,
            'bootstrap' => [
                'message' => $this->settings->composeMessage(),
                'settings' => $this->settings->get(),
                'license' => $this->license->getState(),
            ],
            'i18n' => $this->scriptStrings(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function scriptStrings(): array
    {
        return [
            'appTitle' => __('OT Hello', 'ot-hello'),
            'navDashboard' => __('Dashboard', 'ot-hello'),
            'navSettings' => __('Settings', 'ot-hello'),
            'navHelp' => __('Help', 'ot-hello'),
            'navAbout' => __('About', 'ot-hello'),
            'save' => __('Save changes', 'ot-hello'),
            'saving' => __('Saving…', 'ot-hello'),
            'saved' => __('Settings saved.', 'ot-hello'),
            'retry' => __('Try again', 'ot-hello'),
        ];
    }
}

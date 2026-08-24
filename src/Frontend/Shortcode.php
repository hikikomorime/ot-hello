<?php
/**
 * Public shortcode [ot_hello].
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Frontend;

use OTHello\Settings\SettingsRepository;

final class Shortcode
{
    public function __construct(private readonly SettingsRepository $settings)
    {
    }

    public function register(): void
    {
        add_shortcode('ot_hello', [$this, 'render']);
        add_shortcode('ot_hello_world', [$this, 'render']);
        add_action('wp_enqueue_scripts', [$this, 'registerStyle']);
    }

    public function registerStyle(): void
    {
        wp_register_style(
            'ot-hello-front',
            OT_HELLO_URL . 'assets/frontend.css',
            [],
            OT_HELLO_VERSION
        );
    }

    /**
     * @param array<string, string>|string $atts
     */
    public function render(array|string $atts = []): string
    {
        wp_enqueue_style('ot-hello-front');

        $settings = $this->settings->get();
        $message = $this->settings->composeMessage();

        $html = '<div class="ot-hello-greeting" role="status">';
        $html .= '<p class="ot-hello-greeting__message">' . esc_html($message) . '</p>';

        if ($settings['show_site_info']) {
            $html .= '<p class="ot-hello-greeting__meta">' . esc_html(
                sprintf(
                    /* translators: 1: site name, 2: plugin version */
                    __('Served by %1$s · OT Hello %2$s', 'ot-hello'),
                    (string) get_bloginfo('name'),
                    OT_HELLO_VERSION
                )
            ) . '</p>';
        }

        $html .= '</div>';

        return $html;
    }
}

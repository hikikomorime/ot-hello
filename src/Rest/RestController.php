<?php
/**
 * REST API for greeting settings, status, and license (updates only).
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Rest;

use OTHello\License\LicenseService;
use OTHello\Settings\SettingsRepository;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

final class RestController
{
    public function __construct(
        private readonly SettingsRepository $settings,
        private readonly LicenseService $license
    ) {
    }

    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        register_rest_route(OT_HELLO_REST_NAMESPACE, '/status', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'getStatus'],
                'permission_callback' => [$this, 'canManage'],
            ],
        ]);

        register_rest_route(OT_HELLO_REST_NAMESPACE, '/greeting', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'getGreeting'],
                'permission_callback' => [$this, 'canManage'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'saveGreeting'],
                'permission_callback' => [$this, 'canManage'],
                'args' => [
                    'greeting' => [
                        'type' => 'string',
                        'required' => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'audience' => [
                        'type' => 'string',
                        'required' => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'show_site_info' => [
                        'type' => 'boolean',
                        'required' => false,
                        'sanitize_callback' => 'rest_sanitize_boolean',
                    ],
                ],
            ],
        ]);

        register_rest_route(OT_HELLO_REST_NAMESPACE, '/license', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'getLicense'],
                'permission_callback' => [$this, 'canManage'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'saveLicense'],
                'permission_callback' => [$this, 'canManage'],
                'args' => [
                    'key' => [
                        'type' => 'string',
                        'required' => false,
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'clear' => [
                        'type' => 'boolean',
                        'required' => false,
                        'sanitize_callback' => 'rest_sanitize_boolean',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return bool|WP_Error
     */
    public function canManage()
    {
        if (! current_user_can('manage_options')) {
            return new WP_Error(
                'ot_hello_forbidden',
                __('You are not allowed to manage OT Hello.', 'ot-hello'),
                ['status' => 403]
            );
        }

        return true;
    }

    public function getStatus(): WP_REST_Response
    {
        $settings = $this->settings->get();
        $license = $this->license->getState();

        return new WP_REST_Response([
            'ok' => true,
            'version' => OT_HELLO_VERSION,
            'message' => $this->settings->composeMessage(),
            'settings' => $settings,
            'license' => $license,
            'site' => [
                'name' => (string) get_bloginfo('name'),
                'wpVersion' => (string) get_bloginfo('version'),
                'phpVersion' => PHP_VERSION,
            ],
        ], 200);
    }

    public function getGreeting(): WP_REST_Response
    {
        return new WP_REST_Response($this->greetingPayload(), 200);
    }

    public function saveGreeting(WP_REST_Request $request): WP_REST_Response
    {
        $current = $this->settings->get();
        $input = [
            'greeting' => $request->has_param('greeting')
                ? (string) $request->get_param('greeting')
                : $current['greeting'],
            'audience' => $request->has_param('audience')
                ? (string) $request->get_param('audience')
                : $current['audience'],
            'show_site_info' => $request->has_param('show_site_info')
                ? $request->get_param('show_site_info')
                : $current['show_site_info'],
        ];

        $this->settings->save($input);

        return new WP_REST_Response($this->greetingPayload(), 200);
    }

    public function getLicense(): WP_REST_Response
    {
        return new WP_REST_Response($this->license->getState(), 200);
    }

    public function saveLicense(WP_REST_Request $request): WP_REST_Response
    {
        $key = (string) $request->get_param('key');
        $clear = (bool) $request->get_param('clear');
        $state = $this->license->save($key, $clear);

        return new WP_REST_Response($state, 200);
    }

    /**
     * @return array{message: string, settings: array{greeting: string, audience: string, show_site_info: bool}}
     */
    private function greetingPayload(): array
    {
        return [
            'message' => $this->settings->composeMessage(),
            'settings' => $this->settings->get(),
        ];
    }
}

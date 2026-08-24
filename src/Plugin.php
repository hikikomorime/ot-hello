<?php
/**
 * Plugin bootstrap and service wiring.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello;

use OTHello\Admin\AdminPage;
use OTHello\Frontend\Shortcode;
use OTHello\I18n\I18n;
use OTHello\License\LicenseService;
use OTHello\Rest\RestController;
use OTHello\Settings\SettingsRepository;
use OTHello\Updates\PluginUpdater;

final class Plugin
{
    private static ?self $instance = null;

    private function __construct(private readonly ServiceContainer $container)
    {
    }

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(self::buildContainer());
        }

        return self::$instance;
    }

    public function boot(): void
    {
        add_action('init', [$this->container->get(I18n::class), 'register']);
        add_action('init', [$this->container->get(Shortcode::class), 'register']);
        $this->container->get(AdminPage::class)->register();
        $this->container->get(RestController::class)->register();
        $this->container->get(PluginUpdater::class)->register();
    }

    public function container(): ServiceContainer
    {
        return $this->container;
    }

    private static function buildContainer(): ServiceContainer
    {
        $container = new ServiceContainer();

        $container->set(SettingsRepository::class, static fn (ServiceContainer $_c): SettingsRepository => new SettingsRepository());
        $container->set(LicenseService::class, static fn (ServiceContainer $_c): LicenseService => new LicenseService());
        $container->set(I18n::class, static fn (ServiceContainer $_c): I18n => new I18n());
        $container->set(
            AdminPage::class,
            static fn (ServiceContainer $c): AdminPage => new AdminPage(
                $c->get(SettingsRepository::class),
                $c->get(LicenseService::class)
            )
        );
        $container->set(
            RestController::class,
            static fn (ServiceContainer $c): RestController => new RestController(
                $c->get(SettingsRepository::class),
                $c->get(LicenseService::class)
            )
        );
        $container->set(
            Shortcode::class,
            static fn (ServiceContainer $c): Shortcode => new Shortcode(
                $c->get(SettingsRepository::class)
            )
        );
        $container->set(
            PluginUpdater::class,
            static fn (ServiceContainer $c): PluginUpdater => new PluginUpdater(
                $c->get(LicenseService::class)
            )
        );

        return $container;
    }
}

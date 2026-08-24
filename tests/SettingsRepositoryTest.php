<?php
/**
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Tests;

use OTHello\Settings\SettingsRepository;
use PHPUnit\Framework\TestCase;

final class SettingsRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['ot_hello_options'] = [];
    }

    public function testDefaultsWhenOptionMissing(): void
    {
        $repo = new SettingsRepository();
        $this->assertSame(
            [
                'greeting' => 'Hello',
                'audience' => 'World',
                'show_site_info' => true,
            ],
            $repo->get()
        );
        $this->assertSame('Hello, World!', $repo->composeMessage());
    }

    public function testSaveSanitizesAndTruncates(): void
    {
        $repo = new SettingsRepository();
        $saved = $repo->save([
            'greeting' => '  <b>Hi!</b>  ',
            'audience' => str_repeat('A', 90),
            'show_site_info' => 'no',
        ]);

        $this->assertSame('Hi!', $saved['greeting']);
        $this->assertSame(80, strlen($saved['audience']));
        $this->assertFalse($saved['show_site_info']);
        $this->assertSame($saved, get_option(SettingsRepository::OPTION_KEY));
    }

    public function testComposeUsesFallbackParts(): void
    {
        $repo = new SettingsRepository();
        $repo->save([
            'greeting' => '',
            'audience' => 'Alice',
            'show_site_info' => true,
        ]);

        $this->assertSame('Hello, Alice!', $repo->composeMessage());
    }

    public function testComposeEmptyWhenBothBlank(): void
    {
        $repo = new SettingsRepository();
        $repo->save([
            'greeting' => '',
            'audience' => '',
            'show_site_info' => true,
        ]);

        $this->assertSame('', $repo->composeMessage());
    }

    public function testReadsAndMigratesLegacyOption(): void
    {
        $GLOBALS['ot_hello_options'][SettingsRepository::LEGACY_OPTION_KEY] = [
            'greeting' => 'Cześć',
            'audience' => 'OneThird',
            'show_site_info' => false,
        ];

        $repo = new SettingsRepository();
        $this->assertSame('Cześć, OneThird!', $repo->composeMessage());

        $saved = $repo->save($repo->get());
        $this->assertSame($saved, get_option(SettingsRepository::OPTION_KEY));
        $this->assertFalse(isset($GLOBALS['ot_hello_options'][SettingsRepository::LEGACY_OPTION_KEY]));
    }

    public function testPublicOptionNames(): void
    {
        $this->assertSame('ot_hello_settings', SettingsRepository::OPTION_KEY);
        $this->assertSame('ot_hello_world_settings', SettingsRepository::LEGACY_OPTION_KEY);
    }

    public function testNewSettingsOptionWinsOverLegacy(): void
    {
        $GLOBALS['ot_hello_options']['ot_hello_settings'] = [
            'greeting' => 'New',
            'audience' => 'Site',
            'show_site_info' => true,
        ];
        $GLOBALS['ot_hello_options']['ot_hello_world_settings'] = [
            'greeting' => 'Old',
            'audience' => 'World',
            'show_site_info' => true,
        ];

        $this->assertSame('New, Site!', (new SettingsRepository())->composeMessage());
    }
}

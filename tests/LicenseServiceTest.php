<?php
/**
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Tests;

use OTHello\License\LicenseService;
use PHPUnit\Framework\TestCase;

final class LicenseServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['ot_hello_options'] = [];
    }

    public function testMissingKey(): void
    {
        $service = new LicenseService();
        $this->assertSame(['key' => '', 'status' => 'missing'], $service->getState());
        $this->assertFalse($service->canReceiveOfficialUpdates());
    }

    public function testValidKeyUnlocksUpdatesOnly(): void
    {
        $service = new LicenseService();
        $state = $service->save('ot-hello-abcd-efgh-ijkl-mnop');

        $this->assertSame('saved', $state['status']);
        $this->assertTrue($service->canReceiveOfficialUpdates());
        $this->assertStringStartsWith('OT-HELLO-', $state['key']);
        $this->assertStringContainsString('•', $state['key']);
    }

    public function testInvalidKeyDoesNotUnlockUpdates(): void
    {
        $service = new LicenseService();
        $state = $service->save('not-a-key');

        $this->assertSame('invalid', $state['status']);
        $this->assertFalse($service->canReceiveOfficialUpdates());
    }

    public function testClearingKeyRequiresExplicitFlag(): void
    {
        $service = new LicenseService();
        $service->save('OT-HELLO-AAAA-BBBB-CCCC-DDDD');
        $kept = $service->save('');

        $this->assertSame('saved', $kept['status']);

        $state = $service->save('', true);

        $this->assertSame('missing', $state['status']);
        $this->assertFalse(isset($GLOBALS['ot_hello_options'][LicenseService::OPTION_KEY]));
    }

    public function testLegacyOtHwKeyStillUnlocksUpdates(): void
    {
        $service = new LicenseService();
        $state = $service->save('OT-HW-AAAA-BBBB-CCCC-DDDD');

        $this->assertSame('saved', $state['status']);
        $this->assertTrue($service->canReceiveOfficialUpdates());
        $this->assertStringStartsWith('OT-HW-', $state['key']);
    }

    public function testReadsLegacyOptionKey(): void
    {
        $GLOBALS['ot_hello_options'][LicenseService::LEGACY_OPTION_KEY] = 'OT-HELLO-AAAA-BBBB-CCCC-DDDD';
        $service = new LicenseService();

        $this->assertTrue($service->canReceiveOfficialUpdates());
        $this->assertSame('saved', $service->getState()['status']);
    }

    public function testPublicOptionNames(): void
    {
        $this->assertSame('ot_hello_license', LicenseService::OPTION_KEY);
        $this->assertSame('ot_hello_world_license', LicenseService::LEGACY_OPTION_KEY);
        $this->assertSame('OT-HELLO-', LicenseService::PREFIX);
        $this->assertSame('OT-HW-', LicenseService::LEGACY_PREFIX);
    }

    public function testMigratesLegacyOtHwKeyOnSave(): void
    {
        $GLOBALS['ot_hello_options']['ot_hello_world_license'] = 'OT-HW-AAAA-BBBB-CCCC-DDDD';
        $service = new LicenseService();

        $this->assertTrue($service->canReceiveOfficialUpdates());
        $service->save($service->getKey());

        $this->assertSame('OT-HW-AAAA-BBBB-CCCC-DDDD', get_option('ot_hello_license'));
        $this->assertFalse(isset($GLOBALS['ot_hello_options']['ot_hello_world_license']));
    }

    public function testNewLicenseOptionWinsOverLegacy(): void
    {
        $GLOBALS['ot_hello_options']['ot_hello_license'] = 'OT-HELLO-AAAA-BBBB-CCCC-DDDD';
        $GLOBALS['ot_hello_options']['ot_hello_world_license'] = 'OT-HW-ZZZZ-YYYY-XXXX-WWWW';
        $service = new LicenseService();

        $this->assertSame('OT-HELLO-AAAA-BBBB-CCCC-DDDD', $service->getKey());
    }
}

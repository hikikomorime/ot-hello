<?php
/**
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Tests;

use OTHello\Updates\OneThirdPluginsConfig;
use PHPUnit\Framework\TestCase;

final class OneThirdPluginsConfigTest extends TestCase
{
    public function testCatalogMatchesRewriterPatternAndGitHub(): void
    {
        $this->assertSame('ot-hello', OneThirdPluginsConfig::UPDATE_PLUGIN_SLUG);
        $this->assertSame('/updates/plugins/ot-hello/', OneThirdPluginsConfig::UPDATE_PATH_PREFIX);
        $this->assertSame(
            'https://plugins.onethird.pl/updates/plugins/ot-hello/update.json',
            OneThirdPluginsConfig::updateManifestUrl()
        );
        $this->assertSame(
            'https://plugins.onethird.pl/updates/plugins/ot-hello/latest.json',
            OneThirdPluginsConfig::latestManifestUrl()
        );
        $this->assertSame(
            'https://plugins.onethird.pl/updates/plugins/ot-hello/version.txt',
            OneThirdPluginsConfig::versionTxtUrl()
        );
        $this->assertSame('https://github.com/hikikomorime/ot-hello', OneThirdPluginsConfig::GITHUB_URL);
        $this->assertTrue(OneThirdPluginsConfig::isAllowedHost('plugins.onethird.pl'));
        $this->assertFalse(OneThirdPluginsConfig::isAllowedHost('evil.example'));
    }
}

<?php
/**
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello\Tests;

use OTHello\ServiceContainer;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

final class ServiceContainerTest extends TestCase
{
    public function testGetReturnsSingleton(): void
    {
        $container = new ServiceContainer();
        $container->set(stdClass::class, static fn (ServiceContainer $_c): stdClass => new stdClass());

        $first = $container->get(stdClass::class);
        $second = $container->get(stdClass::class);

        $this->assertSame($first, $second);
    }

    public function testUnknownServiceThrows(): void
    {
        $container = new ServiceContainer();
        $this->expectException(\InvalidArgumentException::class);
        $container->get(stdClass::class);
    }

    public function testWrongTypeThrows(): void
    {
        $container = new ServiceContainer();
        $container->set(stdClass::class, static fn (ServiceContainer $_c): RuntimeException => new RuntimeException('nope'));

        $this->expectException(RuntimeException::class);
        $container->get(stdClass::class);
    }
}

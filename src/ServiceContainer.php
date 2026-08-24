<?php
/**
 * Minimal service container for constructor injection.
 *
 * @package OTHello
 */

declare(strict_types=1);

namespace OTHello;

use InvalidArgumentException;
use RuntimeException;

final class ServiceContainer
{
    /** @var array<class-string, callable(self): object> */
    private array $factories = [];

    /** @var array<class-string, object> */
    private array $instances = [];

    /**
     * @template T of object
     * @param class-string<T>          $id
     * @param callable(self): T        $factory
     */
    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
        unset($this->instances[$id]);
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            /** @var T $existing */
            $existing = $this->instances[$id];

            return $existing;
        }

        if (! isset($this->factories[$id])) {
            throw new InvalidArgumentException(sprintf('Unknown service: %s', $id));
        }

        $service = ($this->factories[$id])($this);

        if (! $service instanceof $id) {
            throw new RuntimeException(sprintf('Service factory for %s returned an unexpected type.', $id));
        }

        $this->instances[$id] = $service;

        return $service;
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]) || isset($this->instances[$id]);
    }
}

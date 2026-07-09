<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Support;

use Rublex\CoreGateway\Contracts\Common\ConfigurableGatewayInterface;
use Rublex\CoreGateway\Data\CredentialSchema;
use Rublex\CoreGateway\Exceptions\GatewayException;
use Rublex\CoreGateway\Exceptions\UnknownGatewayDriverException;

/**
 * Maps a driver key to the class that implements it. Each driver package
 * registers itself from its ServiceProvider, so "which drivers exist" is a
 * function of which packages are installed — not a hardcoded list.
 */
final class GatewayDriverRegistry
{
    /** @var array<string, class-string<ConfigurableGatewayInterface>> */
    private static array $drivers = [];

    /**
     * @param class-string $class
     */
    public static function register(string $class): void
    {
        if (!is_subclass_of($class, ConfigurableGatewayInterface::class)) {
            throw new GatewayException(sprintf(
                '%s cannot be registered: it does not implement %s.',
                $class,
                ConfigurableGatewayInterface::class,
            ));
        }

        self::$drivers[$class::driver()] = $class;
    }

    public static function has(string $driver): bool
    {
        return isset(self::$drivers[$driver]);
    }

    /**
     * @return class-string<ConfigurableGatewayInterface>
     */
    public static function classFor(string $driver): string
    {
        if (!isset(self::$drivers[$driver])) {
            throw UnknownGatewayDriverException::forDriver($driver, self::drivers());
        }

        return self::$drivers[$driver];
    }

    public static function schemaFor(string $driver): CredentialSchema
    {
        return self::classFor($driver)::credentialSchema();
    }

    /**
     * @return array<int, string>
     */
    public static function drivers(): array
    {
        $keys = array_keys(self::$drivers);
        sort($keys);

        return $keys;
    }

    /**
     * @return array<string, class-string<ConfigurableGatewayInterface>>
     */
    public static function all(): array
    {
        return self::$drivers;
    }

    /**
     * Test seam only.
     */
    public static function flush(): void
    {
        self::$drivers = [];
    }
}

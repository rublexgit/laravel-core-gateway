<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Support;

use Rublex\CoreGateway\Contracts\Common\ConfigurableGatewayInterface;
use Rublex\CoreGateway\Contracts\Common\GatewayCredentialResolverInterface;
use Rublex\CoreGateway\Data\GatewayCredentials;

/**
 * Builds a driver instance bound to one provider account.
 *
 * Callers must never resolve driver services out of the service container: a
 * container-resolved singleton has no credentials, and with several accounts per
 * driver there is no single correct instance to hand back.
 */
final class GatewayFactory
{
    public static function fromCredentials(GatewayCredentials $credentials): ConfigurableGatewayInterface
    {
        return GatewayDriverRegistry::classFor($credentials->driver())::fromCredentials($credentials);
    }

    public static function forGatewayId(int $gatewayId, GatewayCredentialResolverInterface $resolver): ConfigurableGatewayInterface
    {
        return self::fromCredentials($resolver->resolve($gatewayId));
    }

    public static function forSlug(string $slug, GatewayCredentialResolverInterface $resolver): ConfigurableGatewayInterface
    {
        return self::fromCredentials($resolver->resolveBySlug($slug));
    }
}

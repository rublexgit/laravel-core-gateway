<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Common;

use Rublex\CoreGateway\Data\GatewayCredentials;

/**
 * Implemented by the host application, which owns the `fiat_gateways` table and
 * the encryption key. Driver packages depend on this contract so they never
 * import an application model.
 *
 * Implementations throw GatewayCredentialException when the gateway is missing.
 */
interface GatewayCredentialResolverInterface
{
    public function resolve(int $gatewayId): GatewayCredentials;

    public function resolveBySlug(string $slug): GatewayCredentials;

    /**
     * Fallback for rows written before gateways carried an id (legacy callbacks):
     * the oldest active gateway for that driver.
     */
    public function resolveDefaultForDriver(string $driver): GatewayCredentials;
}

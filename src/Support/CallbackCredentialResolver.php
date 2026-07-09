<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Support;

use Rublex\CoreGateway\Contracts\Common\GatewayCredentialResolverInterface;
use Rublex\CoreGateway\Data\GatewayCredentials;

/**
 * Rebuilds the account that opened a transaction, so an inbound callback is
 * verified with the very credentials used to sign the outbound request.
 */
final class CallbackCredentialResolver
{
    /**
     * @param mixed $gatewayId The `fiat_gateway_id` recorded on the driver's
     *                         transaction row. Rows written before gateways were
     *                         per-account carry null and fall back to the
     *                         driver's first active gateway.
     */
    public static function forTransaction(
        GatewayCredentialResolverInterface $resolver,
        string $driver,
        mixed $gatewayId,
    ): GatewayCredentials {
        if (is_numeric($gatewayId) && (int) $gatewayId > 0) {
            return $resolver->resolve((int) $gatewayId);
        }

        return $resolver->resolveDefaultForDriver($driver);
    }
}

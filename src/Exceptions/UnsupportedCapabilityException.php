<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Exceptions;

class UnsupportedCapabilityException extends GatewayException
{
    public static function forCapability(string $capability, string $gatewayCode): self
    {
        return new self(
            sprintf('Gateway "%s" does not support capability "%s".', $gatewayCode, $capability)
        );
    }
}

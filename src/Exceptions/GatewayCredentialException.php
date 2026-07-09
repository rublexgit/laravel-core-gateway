<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Exceptions;

class GatewayCredentialException extends GatewayException
{
    public static function missing(string $driver, string $key): self
    {
        return new self(sprintf('Gateway "%s" is missing the required credential "%s".', $driver, $key));
    }

    public static function notFound(string $reference): self
    {
        return new self(sprintf('No credentials found for gateway "%s".', $reference));
    }

    public static function inactive(string $reference): self
    {
        return new self(sprintf('Gateway "%s" is not active.', $reference));
    }
}

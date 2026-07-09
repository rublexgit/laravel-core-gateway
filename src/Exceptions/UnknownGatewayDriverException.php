<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Exceptions;

class UnknownGatewayDriverException extends GatewayException
{
    /**
     * @param array<int, string> $known
     */
    public static function forDriver(string $driver, array $known = []): self
    {
        $suffix = $known !== [] ? sprintf(' Known drivers: %s.', implode(', ', $known)) : '';

        return new self(sprintf('Unknown gateway driver "%s".%s', $driver, $suffix));
    }
}

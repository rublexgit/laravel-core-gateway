<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Http;

interface ConfiguresGatewayHttpInterface
{
    /**
     * Return normalized Guzzle-compatible HTTP options for this gateway,
     * suitable for passing to Http::withOptions().
     *
     * @return array<string, mixed>
     */
    public function gatewayHttpOptions(): array;
}

<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Common;

use Rublex\CoreGateway\Data\CredentialSchema;
use Rublex\CoreGateway\Data\GatewayCredentials;

/**
 * A driver that can be instantiated once per provider account rather than once
 * per process. Implementing this is what allows an admin to register the same
 * driver any number of times with different credentials.
 */
interface ConfigurableGatewayInterface extends GatewayInterface
{
    /**
     * Stable key stored in `fiat_gateways.driver`. Unlike `code()`, this is
     * resolvable without an instance, because the factory needs it before one
     * exists.
     */
    public static function driver(): string;

    public static function credentialSchema(): CredentialSchema;

    public static function fromCredentials(GatewayCredentials $credentials): static;

    public function credentials(): GatewayCredentials;
}

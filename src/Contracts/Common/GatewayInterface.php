<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Common;

use Rublex\CoreGateway\Enums\GatewayType;

interface GatewayInterface
{
    public function code(): string;

    public function type(): GatewayType;
}

<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Enums;

enum GatewayType: string
{
    case FIAT = 'fiat';
    case CRYPTO = 'crypto';
}

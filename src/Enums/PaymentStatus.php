<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELED = 'canceled';
    case EXPIRED = 'expired';
    case UNKNOWN = 'unknown';
}

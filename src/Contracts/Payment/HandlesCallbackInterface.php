<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Payment;

use Rublex\CoreGateway\Data\DynamicDataBag;
use Rublex\CoreGateway\Data\PaymentVerificationResultData;

interface HandlesCallbackInterface
{
    public function handleCallback(DynamicDataBag $payload): PaymentVerificationResultData;
}

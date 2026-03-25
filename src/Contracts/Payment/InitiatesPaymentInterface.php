<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Payment;

use Rublex\CoreGateway\Data\PaymentInitResultData;
use Rublex\CoreGateway\Data\PaymentRequestData;

interface InitiatesPaymentInterface
{
    public function initiate(PaymentRequestData $request): PaymentInitResultData;
}

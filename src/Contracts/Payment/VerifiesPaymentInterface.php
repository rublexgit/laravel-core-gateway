<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Payment;

use Rublex\CoreGateway\Data\PaymentVerificationRequestData;
use Rublex\CoreGateway\Data\PaymentVerificationResultData;

interface VerifiesPaymentInterface
{
    public function verify(PaymentVerificationRequestData $request): PaymentVerificationResultData;
}

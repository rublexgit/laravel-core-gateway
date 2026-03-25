<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Contracts\Payment;

use Rublex\CoreGateway\Data\PaymentVerificationRequestData;
use Rublex\CoreGateway\Data\PaymentVerificationResultData;

interface QueriesPaymentStatusInterface
{
    public function queryStatus(PaymentVerificationRequestData $request): PaymentVerificationResultData;
}

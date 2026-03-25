<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Tests;

use PHPUnit\Framework\TestCase;
use Rublex\CoreGateway\Data\DynamicDataBag;
use Rublex\CoreGateway\Data\PaymentRequestData;
use Rublex\CoreGateway\Exceptions\ValidationException;

final class PaymentRequestDataTest extends TestCase
{
    public function test_it_accepts_valid_input_and_meta(): void
    {
        $meta = new DynamicDataBag(['channel' => 'card']);
        $request = new PaymentRequestData(
            gatewayCode: 'finpay',
            orderId: 'INV-1',
            amount: '100.00',
            currency: 'EUR',
            callbackUrl: 'https://merchant.test/callback',
            meta: $meta
        );

        self::assertSame('finpay', $request->gatewayCode());
        self::assertSame('card', $request->meta()->requireString('channel'));
    }

    public function test_it_rejects_invalid_callback_url(): void
    {
        $this->expectException(ValidationException::class);

        new PaymentRequestData(
            gatewayCode: 'finpay',
            orderId: 'INV-1',
            amount: '100.00',
            currency: 'EUR',
            callbackUrl: 'invalid-url'
        );
    }
}

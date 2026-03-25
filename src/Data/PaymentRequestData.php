<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use Rublex\CoreGateway\Exceptions\ValidationException;

final class PaymentRequestData
{
    public function __construct(
        private readonly string $gatewayCode,
        private readonly string $orderId,
        private readonly string $amount,
        private readonly string $currency,
        private readonly string $callbackUrl,
        private readonly DynamicDataBag $meta = new DynamicDataBag()
    ) {
        $this->validate();
    }

    public function gatewayCode(): string
    {
        return $this->gatewayCode;
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function callbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function meta(): DynamicDataBag
    {
        return $this->meta;
    }

    private function validate(): void
    {
        if (trim($this->gatewayCode) === '') {
            throw new ValidationException('Payment gateway code is required.');
        }

        if (trim($this->orderId) === '') {
            throw new ValidationException('Payment order ID is required.');
        }

        if (!is_numeric($this->amount) || (float) $this->amount <= 0.0) {
            throw new ValidationException('Payment amount must be a positive numeric string.');
        }

        if (trim($this->currency) === '') {
            throw new ValidationException('Payment currency is required.');
        }

        if (filter_var($this->callbackUrl, FILTER_VALIDATE_URL) === false) {
            throw new ValidationException('Payment callback URL is invalid.');
        }
    }
}

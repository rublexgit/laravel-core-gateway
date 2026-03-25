<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use Rublex\CoreGateway\Exceptions\ValidationException;

final class PaymentVerificationRequestData
{
    public function __construct(
        private readonly ?string $transactionId = null,
        private readonly ?string $orderId = null,
        private readonly ?string $gatewayReference = null,
        private readonly DynamicDataBag $meta = new DynamicDataBag()
    ) {
        $this->validate();
    }

    public function transactionId(): ?string
    {
        return $this->transactionId;
    }

    public function orderId(): ?string
    {
        return $this->orderId;
    }

    public function gatewayReference(): ?string
    {
        return $this->gatewayReference;
    }

    public function meta(): DynamicDataBag
    {
        return $this->meta;
    }

    private function validate(): void
    {
        if ($this->transactionId !== null && trim($this->transactionId) === '') {
            throw new ValidationException('Transaction ID must not be empty when provided.');
        }

        if ($this->orderId !== null && trim($this->orderId) === '') {
            throw new ValidationException('Order ID must not be empty when provided.');
        }

        if ($this->gatewayReference !== null && trim($this->gatewayReference) === '') {
            throw new ValidationException('Gateway reference must not be empty when provided.');
        }

        if ($this->transactionId === null && $this->orderId === null && $this->gatewayReference === null) {
            throw new ValidationException(
                'At least one of transactionId, orderId, or gatewayReference is required for verification.'
            );
        }
    }
}

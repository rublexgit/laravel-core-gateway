<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use Rublex\CoreGateway\Enums\PaymentStatus;
use Rublex\CoreGateway\Exceptions\ValidationException;

final class PaymentVerificationResultData
{
    public function __construct(
        private readonly PaymentStatus $status,
        private readonly ?string $transactionId = null,
        private readonly ?string $gatewayReference = null,
        private readonly ?string $message = null,
        private readonly DynamicDataBag $meta = new DynamicDataBag(),
        private readonly DynamicDataBag $raw = new DynamicDataBag()
    ) {
        $this->validate();
    }

    public function status(): PaymentStatus
    {
        return $this->status;
    }

    public function transactionId(): ?string
    {
        return $this->transactionId;
    }

    public function gatewayReference(): ?string
    {
        return $this->gatewayReference;
    }

    public function message(): ?string
    {
        return $this->message;
    }

    public function meta(): DynamicDataBag
    {
        return $this->meta;
    }

    public function raw(): DynamicDataBag
    {
        return $this->raw;
    }

    private function validate(): void
    {
        if ($this->transactionId !== null && trim($this->transactionId) === '') {
            throw new ValidationException('Transaction ID must not be empty when provided.');
        }

        if ($this->gatewayReference !== null && trim($this->gatewayReference) === '') {
            throw new ValidationException('Gateway reference must not be empty when provided.');
        }

        if ($this->message !== null && trim($this->message) === '') {
            throw new ValidationException('Message must not be empty when provided.');
        }
    }
}

<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use Rublex\CoreGateway\Enums\PaymentStatus;
use Rublex\CoreGateway\Exceptions\ValidationException;

final class PaymentInitResultData
{
    public function __construct(
        private readonly PaymentStatus $status,
        private readonly ?string $transactionId = null,
        private readonly ?string $redirectUrl = null,
        private readonly ?string $gatewayReference = null,
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

    public function redirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function gatewayReference(): ?string
    {
        return $this->gatewayReference;
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

        if ($this->redirectUrl !== null && filter_var($this->redirectUrl, FILTER_VALIDATE_URL) === false) {
            throw new ValidationException('Redirect URL must be a valid URL when provided.');
        }

        if ($this->gatewayReference !== null && trim($this->gatewayReference) === '') {
            throw new ValidationException('Gateway reference must not be empty when provided.');
        }
    }
}

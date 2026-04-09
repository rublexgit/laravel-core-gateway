<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use DateTimeImmutable;
use DateTimeInterface;
use Rublex\CoreGateway\Exceptions\ValidationException;

/**
 * Minimal, stable value object representing a payment/callback outcome.
 *
 * This is the **canonical shape** all gateways must use when:
 * - forwarding a payment event to a merchant callback URL
 * - exposing payment status via an HTTP API resource
 * - persisting the forward payload to a JSON column
 *
 * toArray() returns exactly these keys — clients MUST NOT rely on additional
 * fields beyond the contract below:
 *
 * {
 *   "orderId":      string          — canonical payment identifier
 *   "status":       string          — normalized internal status (see PaymentStatus enum values)
 *   "currency":     string          — ISO-4217 uppercase (e.g. "EUR")
 *   "amount":       string          — decimal as string, same precision as stored/expected amount
 *   "errorMessage": string | null   — human-readable failure reason; null when status is not failure
 *   "gatewayCode":  string | null   — identifies the originating gateway (e.g. "aviagram")
 *   "occurredAt":   string | null   — ISO-8601 / ATOM timestamp when this outcome was determined
 *   "raw":          object | null   — provider-specific extras that do not belong in the minimal contract
 * }
 *
 * Provider-specific fields (e.g. Aviagram's method, type, declinedReason, createdAt) MUST be
 * placed in `raw`. The minimal contract fields above are sufficient for any gateway consumer.
 */
final class PaymentOutcomeData
{
    /**
     * @param array<string, mixed>|null $raw Provider-specific fields that do not fit the minimal contract.
     */
    public function __construct(
        private readonly string $orderId,
        private readonly string $status,
        private readonly string $currency,
        private readonly string $amount,
        private readonly ?string $errorMessage = null,
        private readonly ?string $gatewayCode = null,
        private readonly ?DateTimeImmutable $occurredAt = null,
        private readonly ?array $raw = null,
    ) {
        $this->validate();
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function gatewayCode(): ?string
    {
        return $this->gatewayCode;
    }

    public function occurredAt(): ?DateTimeImmutable
    {
        return $this->occurredAt;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function raw(): ?array
    {
        return $this->raw;
    }

    /**
     * Canonical serialization. All consumers (ForwardCallbackJob, HTTP resources,
     * JSON columns) MUST use this method so clients always see the same structure.
     *
     * @return array{
     *     orderId: string,
     *     status: string,
     *     currency: string,
     *     amount: string,
     *     errorMessage: string|null,
     *     gatewayCode: string|null,
     *     occurredAt: string|null,
     *     raw: array<string, mixed>|null
     * }
     */
    public function toArray(): array
    {
        return [
            'orderId'      => $this->orderId,
            'status'       => $this->status,
            'currency'     => $this->currency,
            'amount'       => $this->amount,
            'errorMessage' => $this->errorMessage,
            'gatewayCode'  => $this->gatewayCode,
            'occurredAt'   => $this->occurredAt?->format(DateTimeInterface::ATOM),
            'raw'          => $this->raw,
        ];
    }

    private function validate(): void
    {
        if (trim($this->orderId) === '') {
            throw new ValidationException('Order ID must not be empty.');
        }

        if (trim($this->status) === '') {
            throw new ValidationException('Status must not be empty.');
        }

        if (trim($this->currency) === '') {
            throw new ValidationException('Currency must not be empty.');
        }

        if (trim($this->amount) === '' || !is_numeric($this->amount)) {
            throw new ValidationException('Amount must be a numeric string.');
        }

        if ($this->errorMessage !== null && trim($this->errorMessage) === '') {
            throw new ValidationException('Error message must not be empty when provided.');
        }

        if ($this->gatewayCode !== null && trim($this->gatewayCode) === '') {
            throw new ValidationException('Gateway code must not be empty when provided.');
        }
    }
}

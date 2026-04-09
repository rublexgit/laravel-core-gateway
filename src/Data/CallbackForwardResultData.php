<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use DateTimeImmutable;
use DateTimeInterface;
use Rublex\CoreGateway\Exceptions\ValidationException;

/**
 * Immutable value object representing the outcome of forwarding a payment
 * callback to a merchant's callback URL.
 *
 * Canonical serialization shape (toArray / JSON / API resources):
 * {
 *   "success":      bool,
 *   "httpStatus":   int|null,
 *   "errorMessage": string|null,
 *   "responseBody": string|null,
 *   "respondedAt":  string|null  (ISO-8601 / ATOM format)
 * }
 *
 * All consumers that expose forward-delivery status MUST use toArray() so that
 * clients see one consistent structure regardless of the gateway.
 */
final class CallbackForwardResultData
{
    public function __construct(
        private readonly bool $success,
        private readonly ?int $httpStatus,
        private readonly ?string $errorMessage,
        private readonly ?string $responseBody,
        private readonly ?DateTimeImmutable $respondedAt,
    ) {
        $this->validate();
    }

    /**
     * Builds an instance from a completed HTTP exchange.
     * $successful should reflect whether the HTTP status code is 2xx.
     */
    public static function fromHttpResponse(
        bool $successful,
        int $httpStatus,
        ?string $responseBody,
        DateTimeImmutable $respondedAt,
    ): self {
        return new self(
            success: $successful,
            httpStatus: $httpStatus,
            errorMessage: null,
            responseBody: $responseBody,
            respondedAt: $respondedAt,
        );
    }

    /**
     * Builds an instance from a transport-level exception (no HTTP response received).
     */
    public static function fromException(\Throwable $exception): self
    {
        return new self(
            success: false,
            httpStatus: null,
            errorMessage: $exception->getMessage(),
            responseBody: null,
            respondedAt: null,
        );
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function httpStatus(): ?int
    {
        return $this->httpStatus;
    }

    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function responseBody(): ?string
    {
        return $this->responseBody;
    }

    public function respondedAt(): ?DateTimeImmutable
    {
        return $this->respondedAt;
    }

    /**
     * Returns the canonical array representation for JSON serialization,
     * DB JSON columns, and API resources.
     *
     * @return array{
     *     success: bool,
     *     httpStatus: int|null,
     *     errorMessage: string|null,
     *     responseBody: string|null,
     *     respondedAt: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'success'      => $this->success,
            'httpStatus'   => $this->httpStatus,
            'errorMessage' => $this->errorMessage,
            'responseBody' => $this->responseBody,
            'respondedAt'  => $this->respondedAt?->format(DateTimeInterface::ATOM),
        ];
    }

    private function validate(): void
    {
        if ($this->errorMessage !== null && trim($this->errorMessage) === '') {
            throw new ValidationException('Error message must not be empty when provided.');
        }
    }
}

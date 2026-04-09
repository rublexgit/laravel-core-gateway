<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Rublex\CoreGateway\Data\PaymentOutcomeData;
use Rublex\CoreGateway\Exceptions\ValidationException;

final class PaymentOutcomeDataTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Construction — valid paths
    // -------------------------------------------------------------------------

    public function test_minimal_required_fields_are_accepted(): void
    {
        $dto = new PaymentOutcomeData(
            orderId: 'ORD-001',
            status: 'success',
            currency: 'EUR',
            amount: '100.00',
        );

        self::assertSame('ORD-001', $dto->orderId());
        self::assertSame('success', $dto->status());
        self::assertSame('EUR', $dto->currency());
        self::assertSame('100.00', $dto->amount());
        self::assertNull($dto->errorMessage());
        self::assertNull($dto->gatewayCode());
        self::assertNull($dto->occurredAt());
        self::assertNull($dto->raw());
    }

    public function test_all_optional_fields_are_accepted(): void
    {
        $occurredAt = new DateTimeImmutable('2025-10-09T18:42:00+00:00');
        $raw = ['method' => 'CARD', 'type' => 'TOPUP'];

        $dto = new PaymentOutcomeData(
            orderId: 'ORD-002',
            status: 'failed',
            currency: 'EUR',
            amount: '50.00',
            errorMessage: 'Insufficient funds.',
            gatewayCode: 'aviagram',
            occurredAt: $occurredAt,
            raw: $raw,
        );

        self::assertSame('Insufficient funds.', $dto->errorMessage());
        self::assertSame('aviagram', $dto->gatewayCode());
        self::assertSame($occurredAt, $dto->occurredAt());
        self::assertSame($raw, $dto->raw());
    }

    public function test_unknown_status_is_accepted(): void
    {
        $dto = new PaymentOutcomeData(
            orderId: 'ORD-003',
            status: 'unknown',
            currency: 'EUR',
            amount: '0',
            errorMessage: 'Unrecognised provider status.',
        );

        self::assertSame('unknown', $dto->status());
        self::assertSame('Unrecognised provider status.', $dto->errorMessage());
    }

    // -------------------------------------------------------------------------
    // toArray — canonical shape
    // -------------------------------------------------------------------------

    public function test_to_array_keys_are_stable(): void
    {
        $dto = new PaymentOutcomeData('O', 'pending', 'EUR', '1');

        self::assertSame(
            ['orderId', 'status', 'currency', 'amount', 'errorMessage', 'gatewayCode', 'occurredAt', 'raw'],
            array_keys($dto->toArray()),
        );
    }

    public function test_to_array_null_safe_when_all_optionals_absent(): void
    {
        $result = (new PaymentOutcomeData('ORD-1', 'pending', 'EUR', '10.00'))->toArray();

        self::assertNull($result['errorMessage']);
        self::assertNull($result['gatewayCode']);
        self::assertNull($result['occurredAt']);
        self::assertNull($result['raw']);
    }

    public function test_to_array_formats_occurred_at_as_atom(): void
    {
        $occurredAt = new DateTimeImmutable('2025-10-09T18:42:00+00:00');
        $result = (new PaymentOutcomeData(
            orderId: 'ORD-1',
            status: 'success',
            currency: 'EUR',
            amount: '120.00',
            occurredAt: $occurredAt,
        ))->toArray();

        self::assertSame($occurredAt->format(DateTimeInterface::ATOM), $result['occurredAt']);
    }

    public function test_to_array_full_shape(): void
    {
        $occurredAt = new DateTimeImmutable('2025-10-09T18:42:00+00:00');

        $result = (new PaymentOutcomeData(
            orderId: 'ORD-FULL',
            status: 'success',
            currency: 'EUR',
            amount: '120.00',
            errorMessage: null,
            gatewayCode: 'aviagram',
            occurredAt: $occurredAt,
            raw: ['method' => 'CARD', 'type' => 'TOPUP'],
        ))->toArray();

        self::assertSame([
            'orderId'      => 'ORD-FULL',
            'status'       => 'success',
            'currency'     => 'EUR',
            'amount'       => '120.00',
            'errorMessage' => null,
            'gatewayCode'  => 'aviagram',
            'occurredAt'   => $occurredAt->format(DateTimeInterface::ATOM),
            'raw'          => ['method' => 'CARD', 'type' => 'TOPUP'],
        ], $result);
    }

    // -------------------------------------------------------------------------
    // Validation — required fields
    // -------------------------------------------------------------------------

    public function test_rejects_empty_order_id(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('', 'pending', 'EUR', '10.00');
    }

    public function test_rejects_whitespace_order_id(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('   ', 'pending', 'EUR', '10.00');
    }

    public function test_rejects_empty_status(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('ORD-1', '', 'EUR', '10.00');
    }

    public function test_rejects_empty_currency(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('ORD-1', 'pending', '', '10.00');
    }

    public function test_rejects_empty_amount(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('ORD-1', 'pending', 'EUR', '');
    }

    public function test_rejects_non_numeric_amount(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('ORD-1', 'pending', 'EUR', 'free');
    }

    // -------------------------------------------------------------------------
    // Validation — optional fields
    // -------------------------------------------------------------------------

    public function test_rejects_empty_error_message_when_provided(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('ORD-1', 'failed', 'EUR', '10.00', '');
    }

    public function test_rejects_empty_gateway_code_when_provided(): void
    {
        $this->expectException(ValidationException::class);
        new PaymentOutcomeData('ORD-1', 'pending', 'EUR', '10.00', null, '');
    }
}

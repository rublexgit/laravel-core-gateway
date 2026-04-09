<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Rublex\CoreGateway\Data\CallbackForwardResultData;
use Rublex\CoreGateway\Exceptions\ValidationException;
use RuntimeException;

final class CallbackForwardResultDataTest extends TestCase
{
    // -------------------------------------------------------------------------
    // fromHttpResponse factory
    // -------------------------------------------------------------------------

    public function test_from_http_response_success_path(): void
    {
        $respondedAt = new DateTimeImmutable('2025-10-09T18:42:00+00:00');
        $result = CallbackForwardResultData::fromHttpResponse(
            successful: true,
            httpStatus: 200,
            responseBody: '{"ok":true}',
            respondedAt: $respondedAt,
        );

        self::assertTrue($result->success());
        self::assertSame(200, $result->httpStatus());
        self::assertNull($result->errorMessage());
        self::assertSame('{"ok":true}', $result->responseBody());
        self::assertSame($respondedAt, $result->respondedAt());
    }

    public function test_from_http_response_non_2xx_is_not_success(): void
    {
        $result = CallbackForwardResultData::fromHttpResponse(
            successful: false,
            httpStatus: 503,
            responseBody: 'Service Unavailable',
            respondedAt: new DateTimeImmutable(),
        );

        self::assertFalse($result->success());
        self::assertSame(503, $result->httpStatus());
        self::assertNull($result->errorMessage());
        self::assertSame('Service Unavailable', $result->responseBody());
    }

    public function test_from_http_response_allows_null_response_body(): void
    {
        $result = CallbackForwardResultData::fromHttpResponse(
            successful: true,
            httpStatus: 204,
            responseBody: null,
            respondedAt: new DateTimeImmutable(),
        );

        self::assertNull($result->responseBody());
    }

    // -------------------------------------------------------------------------
    // fromException factory
    // -------------------------------------------------------------------------

    public function test_from_exception_captures_message_and_sets_failure_state(): void
    {
        $exception = new RuntimeException('Connection timed out.');
        $result = CallbackForwardResultData::fromException($exception);

        self::assertFalse($result->success());
        self::assertNull($result->httpStatus());
        self::assertSame('Connection timed out.', $result->errorMessage());
        self::assertNull($result->responseBody());
        self::assertNull($result->respondedAt());
    }

    // -------------------------------------------------------------------------
    // toArray canonical shape
    // -------------------------------------------------------------------------

    public function test_to_array_returns_canonical_shape_on_success(): void
    {
        $respondedAt = new DateTimeImmutable('2025-10-09T18:42:00+00:00');
        $result = CallbackForwardResultData::fromHttpResponse(
            successful: true,
            httpStatus: 200,
            responseBody: '{"received":true}',
            respondedAt: $respondedAt,
        );

        self::assertSame([
            'success'      => true,
            'httpStatus'   => 200,
            'errorMessage' => null,
            'responseBody' => '{"received":true}',
            'respondedAt'  => $respondedAt->format(DateTimeInterface::ATOM),
        ], $result->toArray());
    }

    public function test_to_array_returns_canonical_shape_on_exception(): void
    {
        $result = CallbackForwardResultData::fromException(
            new RuntimeException('Timeout after 30s.')
        );

        self::assertSame([
            'success'      => false,
            'httpStatus'   => null,
            'errorMessage' => 'Timeout after 30s.',
            'responseBody' => null,
            'respondedAt'  => null,
        ], $result->toArray());
    }

    public function test_to_array_keys_are_stable(): void
    {
        $result = CallbackForwardResultData::fromHttpResponse(
            successful: false,
            httpStatus: 422,
            responseBody: null,
            respondedAt: new DateTimeImmutable(),
        );

        self::assertSame(
            ['success', 'httpStatus', 'errorMessage', 'responseBody', 'respondedAt'],
            array_keys($result->toArray()),
        );
    }

    // -------------------------------------------------------------------------
    // Direct constructor + validation
    // -------------------------------------------------------------------------

    public function test_constructor_rejects_empty_error_message(): void
    {
        $this->expectException(ValidationException::class);

        new CallbackForwardResultData(
            success: false,
            httpStatus: null,
            errorMessage: '   ',
            responseBody: null,
            respondedAt: null,
        );
    }

    public function test_constructor_accepts_null_error_message(): void
    {
        $result = new CallbackForwardResultData(
            success: true,
            httpStatus: 200,
            errorMessage: null,
            responseBody: null,
            respondedAt: new DateTimeImmutable(),
        );

        self::assertNull($result->errorMessage());
    }
}

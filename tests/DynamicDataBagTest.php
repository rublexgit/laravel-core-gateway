<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Tests;

use PHPUnit\Framework\TestCase;
use Rublex\CoreGateway\Data\DynamicDataBag;
use Rublex\CoreGateway\Exceptions\ValidationException;

final class DynamicDataBagTest extends TestCase
{
    public function test_it_supports_nested_lookup_and_immutability(): void
    {
        $bag = new DynamicDataBag([
            'order' => [
                'id' => 'INV-1',
            ],
        ]);

        self::assertSame('INV-1', $bag->requireString('order.id'));

        $updated = $bag->with('gateway.trace', 'trace-123');

        self::assertFalse($bag->has('gateway.trace'));
        self::assertSame('trace-123', $updated->requireString('gateway.trace'));
    }

    public function test_it_validates_required_numeric_string(): void
    {
        $bag = new DynamicDataBag(['amount' => '9.99']);
        self::assertSame('9.99', $bag->requireNumericString('amount'));

        $this->expectException(ValidationException::class);
        (new DynamicDataBag(['amount' => 'free']))->requireNumericString('amount');
    }
}

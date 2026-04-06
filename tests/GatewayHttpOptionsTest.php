<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Tests;

use PHPUnit\Framework\TestCase;
use Rublex\CoreGateway\Support\GatewayHttpOptions;

final class GatewayHttpOptionsTest extends TestCase
{
    public function test_returns_empty_array_for_empty_config(): void
    {
        self::assertSame([], GatewayHttpOptions::fromConfig([]));
    }

    public function test_maps_timeout_and_connect_timeout_to_float(): void
    {
        $options = GatewayHttpOptions::fromConfig([
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);

        self::assertSame(30.0, $options['timeout']);
        self::assertSame(10.0, $options['connect_timeout']);
    }

    public function test_omits_zero_and_negative_timeout(): void
    {
        self::assertArrayNotHasKey('timeout', GatewayHttpOptions::fromConfig(['timeout' => 0]));
        self::assertArrayNotHasKey('timeout', GatewayHttpOptions::fromConfig(['timeout' => -5]));
        self::assertArrayNotHasKey('connect_timeout', GatewayHttpOptions::fromConfig(['connect_timeout' => 0]));
    }

    public function test_omits_null_timeout(): void
    {
        $options = GatewayHttpOptions::fromConfig(['timeout' => null, 'connect_timeout' => null]);

        self::assertArrayNotHasKey('timeout', $options);
        self::assertArrayNotHasKey('connect_timeout', $options);
    }

    public function test_maps_verify_bool(): void
    {
        self::assertFalse(GatewayHttpOptions::fromConfig(['verify' => false])['verify']);
        self::assertTrue(GatewayHttpOptions::fromConfig(['verify' => true])['verify']);
    }

    public function test_maps_verify_ca_bundle_path(): void
    {
        $options = GatewayHttpOptions::fromConfig(['verify' => '/etc/ssl/certs/ca-certificates.crt']);

        self::assertSame('/etc/ssl/certs/ca-certificates.crt', $options['verify']);
    }

    public function test_omits_null_verify(): void
    {
        self::assertArrayNotHasKey('verify', GatewayHttpOptions::fromConfig(['verify' => null]));
    }

    public function test_maps_string_proxy(): void
    {
        $options = GatewayHttpOptions::fromConfig(['proxy' => 'http://proxy.example.com:3128']);

        self::assertSame('http://proxy.example.com:3128', $options['proxy']);
    }

    public function test_omits_empty_string_proxy(): void
    {
        self::assertArrayNotHasKey('proxy', GatewayHttpOptions::fromConfig(['proxy' => '']));
        self::assertArrayNotHasKey('proxy', GatewayHttpOptions::fromConfig(['proxy' => '  ']));
    }

    public function test_omits_null_proxy(): void
    {
        self::assertArrayNotHasKey('proxy', GatewayHttpOptions::fromConfig(['proxy' => null]));
    }

    public function test_maps_protocol_array_proxy_with_http_and_https(): void
    {
        $options = GatewayHttpOptions::fromConfig([
            'proxy' => [
                'http' => 'http://proxy.example.com:3128',
                'https' => 'http://proxy.example.com:3129',
                'no' => ['localhost', '127.0.0.1'],
            ],
        ]);

        self::assertSame([
            'http' => 'http://proxy.example.com:3128',
            'https' => 'http://proxy.example.com:3129',
            'no' => ['localhost', '127.0.0.1'],
        ], $options['proxy']);
    }

    public function test_maps_partial_array_proxy_with_only_https(): void
    {
        $options = GatewayHttpOptions::fromConfig([
            'proxy' => [
                'https' => 'http://secure-proxy.example.com:3128',
            ],
        ]);

        self::assertSame(['https' => 'http://secure-proxy.example.com:3128'], $options['proxy']);
    }

    public function test_omits_array_proxy_when_no_valid_protocol_keys(): void
    {
        self::assertArrayNotHasKey('proxy', GatewayHttpOptions::fromConfig(['proxy' => []]));
        self::assertArrayNotHasKey('proxy', GatewayHttpOptions::fromConfig(['proxy' => ['no' => ['localhost']]]));
    }

    public function test_full_config_normalizes_all_fields(): void
    {
        $options = GatewayHttpOptions::fromConfig([
            'timeout' => 45,
            'connect_timeout' => 5,
            'proxy' => 'http://proxy.example.com:8080',
            'verify' => false,
        ]);

        self::assertSame(45.0, $options['timeout']);
        self::assertSame(5.0, $options['connect_timeout']);
        self::assertSame('http://proxy.example.com:8080', $options['proxy']);
        self::assertFalse($options['verify']);
    }
}

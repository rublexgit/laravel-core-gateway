# Laravel Core Gateway

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://packagist.org/packages/rublex/laravel-core-gateway)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Vendor-agnostic abstractions for building fiat and crypto gateway integrations with a single reusable contract layer.

## Purpose

- Shared contracts and capabilities for gateway services.
- Immutable data objects for request and response exchange.
- Generic validation and exception primitives.
- No provider-specific business logic.

## Installation

```bash
composer require rublex/laravel-core-gateway
```

## Contracts Overview

### Common

- `Rublex\CoreGateway\Contracts\Common\GatewayInterface`
  - `code(): string`
  - `type(): GatewayType`

### Payment capabilities

- `Rublex\CoreGateway\Contracts\Payment\InitiatesPaymentInterface`
- `Rublex\CoreGateway\Contracts\Payment\VerifiesPaymentInterface`
- `Rublex\CoreGateway\Contracts\Payment\QueriesPaymentStatusInterface` (optional)
- `Rublex\CoreGateway\Contracts\Payment\HandlesCallbackInterface` (optional)

Use only the capabilities supported by a specific provider package.

## Dynamic Meta Usage

`DynamicDataBag` stores extensible payload fields without adding provider-specific fields to core DTOs.

```php
use Rublex\CoreGateway\Data\DynamicDataBag;

$meta = new DynamicDataBag([
    'customer' => [
        'email' => 'customer@example.com',
    ],
    'provider' => [
        'channel' => 'card',
    ],
]);

$email = $meta->requireString('customer.email');
$meta2 = $meta->with('provider.traceId', 'trace-123');
```

## Fiat/Crypto Readiness

- Gateway category is declared via `GatewayType` (`FIAT` or `CRYPTO`).
- Lifecycle result state is standardized via `PaymentStatus`.
- Provider-specific fields remain in `meta`/`raw` bags, so crypto providers can reuse the same DTOs.

## Example Implementation

```php
use Rublex\CoreGateway\Contracts\Common\GatewayInterface;
use Rublex\CoreGateway\Contracts\Payment\InitiatesPaymentInterface;
use Rublex\CoreGateway\Data\PaymentInitResultData;
use Rublex\CoreGateway\Data\PaymentRequestData;
use Rublex\CoreGateway\Enums\GatewayType;
use Rublex\CoreGateway\Enums\PaymentStatus;

final class ExampleGateway implements GatewayInterface, InitiatesPaymentInterface
{
    public function code(): string
    {
        return 'example';
    }

    public function type(): GatewayType
    {
        return GatewayType::FIAT;
    }

    public function initiate(PaymentRequestData $request): PaymentInitResultData
    {
        // map provider response into the core result shape
        return new PaymentInitResultData(PaymentStatus::PENDING);
    }
}
```

## Migration Notes

- Previous releases exposed only package metadata.
- `v1.0.0` now includes reusable contracts, enums, exceptions, and DTOs for all gateway packages.
- Existing provider packages can keep their public methods and map them to the new contract methods internally.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

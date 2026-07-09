<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use Rublex\CoreGateway\Exceptions\GatewayCredentialException;

/**
 * Everything a driver needs to act as one specific provider account.
 *
 * This is what makes several gateways of the same driver possible: the driver
 * class is stateless with respect to credentials, and one instance is built per
 * `fiat_gateways` row. `secrets` is the encrypted half (API keys, HMAC keys);
 * `settings` is the readable half (base URL, merchant identifiers).
 *
 * Never log this object directly — use toArray(), which redacts secrets.
 */
final class GatewayCredentials
{
    private const REDACTED = '[REDACTED]';

    public const ENVIRONMENT_SANDBOX = 'sandbox';
    public const ENVIRONMENT_PRODUCTION = 'production';

    /**
     * @param array<string, mixed> $secrets
     * @param array<string, mixed> $settings
     */
    public function __construct(
        private readonly string $driver,
        private readonly array $secrets = [],
        private readonly array $settings = [],
        private readonly string $environment = self::ENVIRONMENT_PRODUCTION,
        private readonly ?int $gatewayId = null,
        private readonly ?string $gatewaySlug = null,
    ) {
    }

    public function driver(): string
    {
        return $this->driver;
    }

    /**
     * The `fiat_gateways.id` this instance was built from. Drivers persist it on
     * their transaction rows so an inbound callback can rebuild the same account.
     */
    public function gatewayId(): ?int
    {
        return $this->gatewayId;
    }

    public function gatewaySlug(): ?string
    {
        return $this->gatewaySlug;
    }

    public function environment(): string
    {
        return $this->environment;
    }

    public function isSandbox(): bool
    {
        return $this->environment === self::ENVIRONMENT_SANDBOX;
    }

    public function isProduction(): bool
    {
        return $this->environment === self::ENVIRONMENT_PRODUCTION;
    }

    public function secret(string $key, ?string $default = null): ?string
    {
        $value = $this->secrets[$key] ?? null;

        return is_scalar($value) && trim((string) $value) !== '' ? (string) $value : $default;
    }

    public function requireSecret(string $key): string
    {
        $value = $this->secret($key);

        if ($value === null) {
            throw GatewayCredentialException::missing($this->driver, $key);
        }

        return $value;
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        $value = $this->settings[$key] ?? null;

        if ($value === null || (is_string($value) && trim($value) === '')) {
            return $default;
        }

        return $value;
    }

    public function requireSetting(string $key): string
    {
        $value = $this->setting($key);

        if (!is_scalar($value) || trim((string) $value) === '') {
            throw GatewayCredentialException::missing($this->driver, $key);
        }

        return (string) $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return $this->settings;
    }

    /**
     * Which of the given keys have no usable value. Drivers call this to fail
     * fast with an actionable message instead of signing with an empty key.
     *
     * @param array<int, string> $keys
     *
     * @return array<int, string>
     */
    public function missing(array $keys): array
    {
        $missing = [];

        foreach ($keys as $key) {
            if ($this->secret($key) === null && $this->setting($key) === null) {
                $missing[] = $key;
            }
        }

        return $missing;
    }

    /**
     * Stable content hash over the credential material. Used to detect that the
     * same provider account was registered twice, and to display "which key is
     * live" without decrypting anything.
     */
    public function fingerprint(): string
    {
        $canonical = ['driver' => $this->driver, 'environment' => $this->environment];

        $secrets = $this->secrets;
        $settings = $this->settings;
        ksort($secrets);
        ksort($settings);
        $canonical['secrets'] = $secrets;
        $canonical['settings'] = $settings;

        return hash('sha256', (string) json_encode($canonical, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Safe for logs: secret values are replaced with a placeholder.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'gateway_id' => $this->gatewayId,
            'gateway_slug' => $this->gatewaySlug,
            'environment' => $this->environment,
            'settings' => $this->settings,
            'secrets' => array_map(static fn (): string => self::REDACTED, $this->secrets),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }
}

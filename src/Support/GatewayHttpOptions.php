<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Support;

final class GatewayHttpOptions
{
    /**
     * Normalize a raw HTTP config array into Guzzle-compatible options
     * suitable for use with Http::withOptions().
     *
     * Accepts:
     *   - timeout        (int|float > 0)
     *   - connect_timeout (int|float > 0)
     *   - proxy          (non-empty string URL  OR  array with 'http'/'https' keys, optional 'no')
     *   - verify         (bool  OR  non-empty string path to CA bundle)
     *
     * Null values and invalid entries are silently omitted from the output.
     *
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    public static function fromConfig(array $raw): array
    {
        $options = [];

        if (isset($raw['timeout']) && is_numeric($raw['timeout']) && (float) $raw['timeout'] > 0) {
            $options['timeout'] = (float) $raw['timeout'];
        }

        if (isset($raw['connect_timeout']) && is_numeric($raw['connect_timeout']) && (float) $raw['connect_timeout'] > 0) {
            $options['connect_timeout'] = (float) $raw['connect_timeout'];
        }

        if (isset($raw['verify']) && (is_bool($raw['verify']) || (is_string($raw['verify']) && trim($raw['verify']) !== ''))) {
            $options['verify'] = $raw['verify'];
        }

        if (isset($raw['proxy'])) {
            $proxy = $raw['proxy'];

            if (is_string($proxy) && trim($proxy) !== '') {
                $options['proxy'] = $proxy;
            } elseif (is_array($proxy)) {
                $normalized = [];

                if (isset($proxy['http']) && is_string($proxy['http']) && trim($proxy['http']) !== '') {
                    $normalized['http'] = $proxy['http'];
                }

                if (isset($proxy['https']) && is_string($proxy['https']) && trim($proxy['https']) !== '') {
                    $normalized['https'] = $proxy['https'];
                }

                if ($normalized !== [] && isset($proxy['no']) && is_array($proxy['no'])) {
                    $normalized['no'] = $proxy['no'];
                }

                if ($normalized !== []) {
                    $options['proxy'] = $normalized;
                }
            }
        }

        return $options;
    }
}

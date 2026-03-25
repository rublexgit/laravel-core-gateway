<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use Rublex\CoreGateway\Exceptions\ValidationException;

final class DynamicDataBag
{
    /**
     * @var array<string, mixed>
     */
    private array $items;

    /**
     * @param array<string, mixed> $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            if (!is_string($key) || trim($key) === '') {
                throw new ValidationException('DynamicDataBag keys must be non-empty strings.');
            }
        }

        $this->items = $items;
    }

    /**
     * @param array<string, mixed> $items
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }

    public function has(string $key): bool
    {
        $this->tryGet($key, $exists);

        return $exists === true;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->tryGet($key, $exists);

        return $exists ? $value : $default;
    }

    public function with(string $key, mixed $value): self
    {
        if (trim($key) === '') {
            throw new ValidationException('DynamicDataBag key must not be empty.');
        }

        $items = $this->items;
        $segments = explode('.', $key);
        $lastSegment = array_pop($segments);

        if ($lastSegment === null || $lastSegment === '') {
            throw new ValidationException('DynamicDataBag key must not be empty.');
        }

        $pointer = &$items;
        foreach ($segments as $segment) {
            if ($segment === '') {
                throw new ValidationException('DynamicDataBag key contains an invalid segment.');
            }

            if (!isset($pointer[$segment])) {
                $pointer[$segment] = [];
            }

            if (!is_array($pointer[$segment])) {
                throw new ValidationException(
                    sprintf('Cannot set nested key "%s" because "%s" is not an array.', $key, $segment)
                );
            }

            $pointer = &$pointer[$segment];
        }

        $pointer[$lastSegment] = $value;

        return new self($items);
    }

    public function requireString(string $key): string
    {
        $value = $this->getRequiredValue($key);
        if (!is_string($value) || trim($value) === '') {
            throw new ValidationException(sprintf('Expected non-empty string at key "%s".', $key));
        }

        return $value;
    }

    public function requireNumericString(string $key): string
    {
        $value = $this->requireString($key);
        if (!is_numeric($value)) {
            throw new ValidationException(sprintf('Expected numeric string at key "%s".', $key));
        }

        return $value;
    }

    /**
     * @return array<mixed>
     */
    public function requireArray(string $key): array
    {
        $value = $this->getRequiredValue($key);
        if (!is_array($value)) {
            throw new ValidationException(sprintf('Expected array at key "%s".', $key));
        }

        return $value;
    }

    private function getRequiredValue(string $key): mixed
    {
        $value = $this->tryGet($key, $exists);
        if (!$exists) {
            throw new ValidationException(sprintf('Missing required key "%s".', $key));
        }

        return $value;
    }

    private function tryGet(string $key, ?bool &$exists = null): mixed
    {
        if (trim($key) === '') {
            throw new ValidationException('DynamicDataBag key must not be empty.');
        }

        $segments = explode('.', $key);
        $current = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                $exists = false;

                return null;
            }

            $current = $current[$segment];
        }

        $exists = true;

        return $current;
    }
}

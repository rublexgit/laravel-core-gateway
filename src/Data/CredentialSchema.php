<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

/**
 * The full set of inputs one driver needs per provider account.
 *
 * The schema is the single source of truth for three consumers: the admin API
 * (validation), the admin panel (form rendering), and the driver itself (which
 * keys it may read). Adding a field to a driver therefore lights it up in the
 * wizard without touching the frontend.
 */
final class CredentialSchema
{
    /** @var array<int, CredentialField> */
    private array $fields;

    public function __construct(CredentialField ...$fields)
    {
        $this->fields = array_values($fields);
    }

    public static function make(CredentialField ...$fields): self
    {
        return new self(...$fields);
    }

    /**
     * @return array<int, CredentialField>
     */
    public function fields(): array
    {
        return $this->fields;
    }

    public function field(string $key): ?CredentialField
    {
        foreach ($this->fields as $field) {
            if ($field->key() === $key) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_map(static fn (CredentialField $f): string => $f->key(), $this->fields);
    }

    /**
     * @return array<int, string>
     */
    public function secretKeys(): array
    {
        return array_values(array_map(
            static fn (CredentialField $f): string => $f->key(),
            array_filter($this->fields, static fn (CredentialField $f): bool => $f->isSecret()),
        ));
    }

    /**
     * @return array<int, string>
     */
    public function settingKeys(): array
    {
        return array_values(array_map(
            static fn (CredentialField $f): string => $f->key(),
            array_filter($this->fields, static fn (CredentialField $f): bool => !$f->isSecret()),
        ));
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<string, string> Field key => error message. Empty when valid.
     */
    public function validate(array $values): array
    {
        $errors = [];

        foreach ($this->fields as $field) {
            $error = $field->validate($values[$field->key()] ?? null);
            if ($error !== null) {
                $errors[$field->key()] = $error;
            }
        }

        return $errors;
    }

    /**
     * Splits a flat submitted payload into the encrypted half and the readable
     * half, dropping any key the driver did not declare. Absent optional fields
     * fall back to their declared default.
     *
     * @param array<string, mixed> $values
     *
     * @return array{secrets: array<string, mixed>, settings: array<string, mixed>}
     */
    public function partition(array $values): array
    {
        $secrets = [];
        $settings = [];

        foreach ($this->fields as $field) {
            $key = $field->key();

            $value = array_key_exists($key, $values) ? $values[$key] : null;
            if ($value === null || (is_string($value) && trim($value) === '')) {
                $value = $field->default();
            }

            if ($value === null) {
                continue;
            }

            if ($field->isSecret()) {
                $secrets[$key] = $value;
            } else {
                $settings[$key] = $value;
            }
        }

        return ['secrets' => $secrets, 'settings' => $settings];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return array_map(static fn (CredentialField $f): array => $f->toArray(), $this->fields);
    }
}

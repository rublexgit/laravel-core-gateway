<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Data;

use Rublex\CoreGateway\Enums\CredentialFieldType;

/**
 * One input a driver needs in order to talk to a single provider account.
 *
 * `secret: true` fields are encrypted at rest, never returned by the admin API,
 * and only ever displayed masked. Everything else is plain settings (base URL,
 * timeouts) that an operator is expected to read back.
 */
final class CredentialField
{
    /**
     * @param array<int, array{value: string, label: string}> $options Only meaningful for SELECT.
     */
    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly CredentialFieldType $type = CredentialFieldType::TEXT,
        private readonly bool $required = true,
        private readonly bool $secret = false,
        private readonly mixed $default = null,
        private readonly array $options = [],
        private readonly ?string $help = null,
    ) {
    }

    public static function secret(string $key, string $label, ?string $help = null, bool $required = true): self
    {
        return new self($key, $label, CredentialFieldType::PASSWORD, $required, true, null, [], $help);
    }

    public static function text(string $key, string $label, ?string $help = null, bool $required = true, mixed $default = null): self
    {
        return new self($key, $label, CredentialFieldType::TEXT, $required, false, $default, [], $help);
    }

    public static function url(string $key, string $label, ?string $help = null, bool $required = true, ?string $default = null): self
    {
        return new self($key, $label, CredentialFieldType::URL, $required, false, $default, [], $help);
    }

    public static function number(string $key, string $label, ?string $help = null, bool $required = false, mixed $default = null): self
    {
        return new self($key, $label, CredentialFieldType::NUMBER, $required, false, $default, [], $help);
    }

    public static function boolean(string $key, string $label, ?string $help = null, mixed $default = null): self
    {
        return new self($key, $label, CredentialFieldType::BOOLEAN, false, false, $default, [], $help);
    }

    /**
     * @param array<int, array{value: string, label: string}> $options
     */
    public static function select(string $key, string $label, array $options, ?string $help = null, bool $required = true, mixed $default = null): self
    {
        return new self($key, $label, CredentialFieldType::SELECT, $required, false, $default, $options, $help);
    }

    public function key(): string
    {
        return $this->key;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function type(): CredentialFieldType
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isSecret(): bool
    {
        return $this->secret;
    }

    public function default(): mixed
    {
        return $this->default;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function options(): array
    {
        return $this->options;
    }

    public function help(): ?string
    {
        return $this->help;
    }

    /**
     * Returns a validation error message, or null when the value is acceptable.
     * An absent optional field always validates.
     */
    public function validate(mixed $value): ?string
    {
        $isBlank = $value === null || (is_string($value) && trim($value) === '');

        if ($isBlank) {
            return $this->required ? sprintf('%s is required.', $this->label) : null;
        }

        return match ($this->type) {
            CredentialFieldType::URL => filter_var((string) $value, FILTER_VALIDATE_URL) === false
                ? sprintf('%s must be a valid URL.', $this->label)
                : null,
            CredentialFieldType::NUMBER => !is_numeric($value)
                ? sprintf('%s must be numeric.', $this->label)
                : null,
            CredentialFieldType::BOOLEAN => !is_bool($value) && !in_array($value, ['0', '1', 0, 1, 'true', 'false'], true)
                ? sprintf('%s must be a boolean.', $this->label)
                : null,
            CredentialFieldType::SELECT => !in_array((string) $value, array_column($this->options, 'value'), true)
                ? sprintf('%s must be one of: %s.', $this->label, implode(', ', array_column($this->options, 'value')))
                : null,
            default => null,
        };
    }

    /**
     * Shape consumed by the admin panel to render the credential step of the
     * gateway wizard. Never carries a value — only the field definition.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type->value,
            'required' => $this->required,
            'secret' => $this->secret,
            'default' => $this->default,
            'options' => $this->options,
            'help' => $this->help,
        ];
    }
}

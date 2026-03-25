<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Abstracts;

use Rublex\CoreGateway\Exceptions\ValidationException;

abstract class AbstractGateway
{
    protected function assertNonEmptyString(string $value, string $field): string
    {
        if (trim($value) === '') {
            throw new ValidationException(sprintf('%s is required.', $field));
        }

        return $value;
    }

    protected function assertPositiveNumericString(string $value, string $field): string
    {
        if (!is_numeric($value) || (float) $value <= 0.0) {
            throw new ValidationException(sprintf('%s must be a positive numeric string.', $field));
        }

        return $value;
    }

    protected function assertUrl(string $value, string $field): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            throw new ValidationException(sprintf('%s must be a valid URL.', $field));
        }

        return $value;
    }
}

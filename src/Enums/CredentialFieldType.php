<?php

declare(strict_types=1);

namespace Rublex\CoreGateway\Enums;

/**
 * Input type a credential field expects. Drives both server-side validation and
 * the widget the admin panel renders in the gateway wizard.
 */
enum CredentialFieldType: string
{
    case TEXT = 'text';
    case PASSWORD = 'password';
    case URL = 'url';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
}

# Laravel Core Gateway

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://packagist.org/packages/rublex/laravel-core-gateway)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Core payment package for shared installation across all Laravel gateway packages.

## Overview

- Provides a single shared package for payment gateway projects.
- Designed to be installed in all gateway packages.
- Keeps dependency management centralized at the core level.
- Contains no classes and no interfaces by design.

## Installation

Install this package in any gateway package:

```bash
composer require rublex/laravel-core-gateway
```

## Use In Other Packages

Add this package as a dependency in each gateway package `composer.json`:

```json
{
  "require": {
    "rublex/laravel-core-gateway": "^1.0"
  }
}
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

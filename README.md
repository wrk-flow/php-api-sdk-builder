### Build unified APIs with dependency injection and strict code in mind.

```bash
composer require wrkflow/php-api-sdk-builder
```

> This package is still in its early stages. But the main concepts will probably remain same.

This package helps you build PHP clients for external APIs with dependency injection and type strict code in mind.

## Why to use?

- If you want to consume external API without losing type strict code (Data transfer objects instead of array keys).
- If you work with multiple APIs and want to use the same conventions.
- If you want to provide type strict way to build request parameters.
- If you want to save time building HTTP/s request.

## Features

![img](https://img.shields.io/badge/PHPStan-8-blue)
![php](https://img.shields.io/badge/PHP-8.1-B0B3D6)
![coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/pionl/bdae700e21749c3532d7961f1d8b252b/raw/coverage.json)

- 🛠 Dependency injection using your favorite framework (Laravel, _PR for more_)
- ✅ Uses PSR packages you already use for HTTP/S communication
- 🏆 Forcing type strict implementation for input (request options) and output (`Response`)
- 🎗 Encouraging `Data transfer objects`
- 🎭 Re-usable and configurable headers using objects

[📖 Read the documentation](https://php-sdk-builder.wrk-flow.com)

## APIs packages

> List of APIs made with this package

- Maggelano Channel manager - _TODO_

## LaraStrict class placement

With `phpstan/extension-installer`, the SDK placement extension loads
automatically when the LaraStrict `ClassPlacementExtension` interface is available.
Without that interface, the configuration returns no services. Runtime SDK use
does not require LaraStrict.

Without the extension installer, add this file after LaraStrict in your
application's `phpstan.neon`:

```neon
includes:
    - vendor/larastrict/conventions/extension.neon
    - vendor/wrkflow/php-api-sdk-builder/phpstan/extension.php
```

Entities may then stay beside their endpoint in `Endpoints/{endpoint}`, including
`App/Confirme/Sdk/Endpoints/Verifiers/DeviceEntity.php`. Shared Entities keep their
`Entities` folder. The extension also supports SDK roots named `Api` or another
name. It requires an endpoint folder and preserves the other placement rules.

For extension development, install a compatible `larastrict/conventions` version
as a development dependency, then run `composer test:larastrict`. These tests
check the SDK allowance against the actual LaraStrict placement rule.

## Development

For development read [CONTRIBUTIONS](https://php-sdk-builder.wrk-flow.com/contributions)

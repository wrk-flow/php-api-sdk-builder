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

![img](https://img.shields.io/badge/PHPStan-9-blue)
![php](https://img.shields.io/badge/PHP-8.1-B0B3D6)
![coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/pionl/bdae700e21749c3532d7961f1d8b252b/raw/coverage.json)

- 🛠 Dependency injection using your favorite framework (Laravel, _PR for more_)
- ✅ Uses PSR packages you already use for HTTP/S communication
- 🏆 Forcing type strict implementation for input (request options) and output (`Response`)
- 🎗 Encouraging `Data transfer objects`
- 🎭 Re-usable and configurable headers using objects

[📖 Read the documentation](https://php-sdk-builder.wrk-flow.com)

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
`App/Orders/Sdk/Endpoints/Devices/DeviceEntity.php`. Shared Entities keep their
`Entities` folder. The extension also supports SDK roots named `Api` or another
name. It requires an endpoint folder and preserves the other placement rules.

The adapter checks SDK constructor arguments by type and requires their factory
names: endpoint `$di`, and response `$response`, `$body`, or `$container` when
those arguments are accepted. `GetValue` in a response constructor is named
`$body`; unrelated inputs and methods keep their names. Constructors may accept
additional injected collaborators. Those
collaborators receive naming and container-lookup checks. Endpoints and Responses
are constructed through the SDK factory, so the adapter does not add them to the
plain container-resolvability rule.

The package registers the general `api-sdk-builder` skill through
`extra.strictphp/conventions.skills`. The `strictphp-sync-ai` command discovers it
with the shared PHP skill. Read the skill for SDK conventions; LaraStrict-specific
integration applies only when LaraStrict is installed.

For extension development, install a compatible `larastrict/conventions` version
as a development dependency, then run `composer test:larastrict`. These tests
check the SDK allowance against the actual LaraStrict placement rule.

## Development

Development uses `strictphp/conventions:^2.1` for Rector, ECS, and PHPStan.
Install development dependencies with PHP 8.3 or later. Runtime consumers can
continue to use PHP 8.1 or later without the development tools.

Run `composer qa` to apply Rector, then ECS, then run PHPStan and tests.
Use `composer lint:upgrade:check`, `composer lint:check`, `composer analyse`,
and `composer test` for validation without code transformations.
Laravel dependencies support the adapter tests; the SDK does not require
`larastrict/conventions` for its normal development workflow.

For development read [CONTRIBUTIONS](https://php-sdk-builder.wrk-flow.com/contributions)

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

## Large XML responses

Implement `BodyIsStreamedXmlInterface` on a response to skip the default whole-document XML parser. Construct `StreamedXmlBody` with the PSR response. Call `readHeader(['Success', 'Errors'])` to validate the document and read selected root children before applying updates. Use `elements(['RatePlans', 'RatePlan'])` to read matching elements one at a time. If attributes identify the elements you need, pass a filter as the second argument. The filter receives the XMLReader on the matching element and must leave it on that node. This feature requires XMLReader and a seekable response body.

Use `mapElements($path, $map)` when you only need attributes or an XML fragment for another decoder. The callback receives the XMLReader on the matching element and must leave it there. This avoids importing the subtree into SimpleXML.

On PHP 8.1 through 8.3, `XmlReaderStream` provides the URI wrapper that connects XMLReader to the PSR stream. PHP 8.4 and later use `XMLReader::fromStream()` directly. When the DOM extension is available, the parser imports each subtree into SimpleXML without a second XML parse. With only XMLReader and SimpleXML, it parses the serialized XML of each matching element.

The SDK Guzzle middleware keeps responses up to 2 MiB in memory and spills larger responses to a private temporary directory. A disk buffer keeps the HTTP body seekable while limiting PHP heap usage. The parser can validate the complete response, rewind it, then load one matching XML subtree at a time. Any framework or standalone PHP application can install the middleware on its Guzzle client.

```php
use GuzzleHttp\Client;
use WrkFlow\ApiSdkBuilder\Http\ResponseBufferMiddleware;
use WrkFlow\ApiSdkBuilder\Http\TemporaryResponseStorage;

$storage = new TemporaryResponseStorage('/path/to/private/response-tmp');
$middleware = new ResponseBufferMiddleware($storage);
$client = new Client(['handler' => $middleware->stack()]);
```

For an existing Guzzle handler stack, call `$stack->push($middleware)`. Calling `close()` on a response body deletes its buffer file. The same cleanup happens when PHP destroys the stream after its last reference is released. A response, iterator, or other retained reference can keep the stream and file alive. Closing XMLReader leaves the HTTP body available to those callers. If a process is killed before destruction, schedule `$storage->clearExpired(time() - 86400)` to remove abandoned buffers older than 24 hours. Cleanup skips locked files.

Laravel applications can enable the SDK Guzzle middleware with `API_SDK_RESPONSE_BUFFERS_ENABLED=true`. By default, buffering uses `storage/app/api-sdk-response-tmp`. Set `API_SDK_RESPONSE_BUFFER_DIRECTORY` to use a different private, writable directory. If the application binds its own Guzzle client, add the middleware to that client's handler stack. Laravel then schedules `api-sdk:response-buffers:clear` hourly.

### Benchmark rate-plan XML

Run the benchmark from an SDK source checkout with its Composer dependencies installed. SDK distribution archives exclude the benchmark tool. Use an existing response or generate a temporary one from a sample. The generator defaults to approximately 4 MiB and deletes the generated file after the run.

```bash
php tools/benchmark-rate-plan-xml.php compare /path/to/response.xml
php tools/benchmark-rate-plan-xml.php compare-sample /path/to/sample.xml
php tools/benchmark-rate-plan-xml.php compare-sample /path/to/sample.xml 32
```

The tool uses a fresh PHP process for each mode. It reports validation time, iteration time, peak PHP heap, peak process RSS, and rate counts. Compare counts and digests before comparing performance. The buffered mode reports the buffer write time separately and verifies that PHP deletes the spill file when it releases the response.

The legacy parser can consume several times the file size in memory. For larger files you can run one mode. An optional final mode after the autoloader also works with `compare-sample`.

```bash
php tools/benchmark-rate-plan-xml.php buffered /path/to/response.xml
php tools/benchmark-rate-plan-xml.php compare-sample /path/to/sample.xml 128 vendor/autoload.php buffered
```

## Optional LaraStrict integration

Class-placement rules belong to `larastrict/conventions`. This optional adapter
extends those rules for SDK classes. Shared development tools and AI registration
use `strictphp/conventions`.

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

For extension development, install a compatible `larastrict/conventions` version
as a development dependency, then run `composer test:larastrict`. These tests
check the SDK allowance against the actual LaraStrict placement rule.

## AI conventions

The package registers the general `api-sdk-builder` skill through
`extra.strictphp/conventions.skills`. To install the AI instructions in your
application, use PHP 8.3 or later and install StrictPHP as a development dependency:

```bash
composer require --dev strictphp/conventions:^2.1
vendor/bin/strictphp-sync-ai
```

Installing the SDK alone does not install StrictPHP. The sync command installs
the SDK skill with the shared PHP skill and collects package rules in `AGENTS.md`.
This works with or without LaraStrict. Read the skill for SDK conventions;
LaraStrict-specific integration applies only when LaraStrict is installed.

Run the sync command again after dependency updates, or configure Composer hooks
as described in the [StrictPHP setup guide](https://github.com/strictphp/conventions/blob/main/doc/setup-guide.md).
Use `vendor/bin/strictphp-sync-ai --check` to check that the instructions are current.

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

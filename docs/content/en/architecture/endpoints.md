---
title: Endpoints
category: Architecture
position: 13
---

An endpoint groups related HTTP operations. Extend `AbstractEndpoint`, implement
`basePath()`, and return a concrete response type from each public operation.

## Implementation

Keep the endpoint, its Options, Responses, and endpoint-specific Entities in
`Endpoints/{endpoint}`. For example:

```php
namespace ExampleSdk\Endpoints\Units;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;

final class UnitsEndpoint extends AbstractEndpoint
{
    public function get(string $id): UnitResponse
    {
        return $this->sendGet(
            responseClass: UnitResponse::class,
            uri: $this->uri(rawurlencode($id)),
            headers: [new JsonHeaders()],
        );
    }

    protected function basePath(): string
    {
        return 'units';
    }
}
```

The SDK factory supplies the endpoint's inherited `EndpointDIEntity $di`.
Keep `$di` if you define a constructor, and pass it to the parent. Inject other
collaborators through the constructor. Public operation arguments are caller
inputs, not method injection points.

## HTTP operations

Use `sendGet()`, `sendPost()`, `sendPut()`, or `sendDelete()`. Each accepts a
`responseClass`, PSR `UriInterface`, headers, and an optional
`expectedResponseStatusCode`. Body methods accept `OptionsInterface`, a PSR
`StreamInterface`, a string, or `null`.

`uri()` combines the environment URI with `basePath()` and an optional path
suffix. Encode caller-supplied path segments. Use PSR URI methods such as
`withQuery()` when adding a query string.

The default accepted status codes are 200 and 201. Set
`expectedResponseStatusCode` to a status code or array of codes when the API
uses another successful status. Unexpected status codes raise the API's
failed-response exception. See [Responses](/architecture/responses).

## JSON Options

Extend `AbstractJsonOptions` and implement `toArray(AbstractEnvironment $environment)`:

```php
namespace ExampleSdk\Endpoints\Units;

use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Options\AbstractJsonOptions;

final class PageOptions extends AbstractJsonOptions
{
    public function __construct(public int $page = 1, public ?int $perPage = null)
    {
    }

    public function toArray(AbstractEnvironment $environment): array
    {
        return array_filter([
            'page' => $this->page,
            'per_page' => $this->perPage,
        ], static fn (mixed $value): bool => $value !== null);
    }
}
```

Options are caller-supplied data. Preserve the API's field names and types.
`toBody()` serializes the array as JSON. `MergedJsonOptions` combines multiple
JSON Options, including nullable entries.

## Optional LaraStrict integration

The package's `phpstan/extension.php` loads placement and constructor checks when
compatible LaraStrict conventions are installed. Entities may live in endpoint
folders; shared Entities may use the domain's `Entities` namespace. Runtime SDK
use and the package's StrictPHP development tooling do not require LaraStrict.

---
title: Responses
category: Architecture
position: 14
---

A response converts an HTTP result into typed application data. Extend
`AbstractResponse` for a raw response, `AbstractJsonResponse` for JSON, or
`AbstractXMLResponse` for XML. `getResponse()` returns the underlying PSR response.

XML responses require PHP's SimpleXML extension (`ext-simplexml`). Install and
enable it in each environment that uses XML responses.

## Status codes and errors

The request action accepts status codes 200 and 201 by default. An endpoint can
set `expectedResponseStatusCode` to one code or an array. For an unexpected code,
`AbstractApi::createFailedResponseException()` selects `BadRequestException` for
4xx responses and `ServerFailedException` for other codes.

The request action wraps exceptions from body parsing and response construction
in `ResponseException`, preserving the original cause. Existing
`ResponseException` instances pass through unchanged. PHP `Error` instances,
including `TypeError`, propagate unchanged. A response class does not need an
`isSuccessful()` method to participate in this flow.

## JSON response

The SDK parses the JSON body into `GetValue` before constructing your response.
Keep constructor arguments named `$response` and `$body`; the factory supplies
them by name. Parse required fields in the constructor:

```php
namespace ExampleSdk\Endpoints\Units;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractJsonResponse;
use Wrkflow\GetValue\GetValue;

final class UnitResponse extends AbstractJsonResponse
{
    public readonly int $id;
    public readonly string $name;

    public function __construct(ResponseInterface $response, GetValue $body)
    {
        parent::__construct($response, $body);
        $this->id = $body->getRequiredInt('id');
        $this->name = $body->getRequiredString('name');
    }
}
```

`body()` returns the parsed `GetValue`. Invalid JSON raises
`InvalidJsonResponseException`. Required getters validate missing or invalid
fields. Convert nested records into typed Entities when useful.

## Responses containing items

Extend `AbstractJsonItemsResponse` or `AbstractXMLItemsResponse`. Their
constructors accept `$response`, `$body`, and `$container`. Keep these names.

Implement `getTransformerClass(): string` with the class of a
`GetValueTransformerContract`, and `getItemsKey(): string|array` with the field
path containing the items. The SDK container creates the transformer.

Implement `items(): array` by returning `transformUsingArray()`, with a PHPDoc
return type naming your Entity. Implement `loopItems(Closure $onItem): bool` by
calling `transformUsingLoop($onItem)`. `getRawItems()` provides untransformed
items. Document the callback's Entity type in your concrete response.

## Pagination

Use `Responses\Concerns\PaginatedResponse` for pagination accessors. Initialize
`$itemsPerPage`, `$totalItems`, `$currentPage`, and `$totalPages` in your response
constructor from the API's metadata.

The trait exposes `getItemsPerPage()`, `getTotalItems()`, `getCurrentPage()`,
`getTotalPages()`, and `onLastPage()`. An empty result counts as the last page.

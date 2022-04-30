---
title: Endpoints
category: Architecture
position: 13
---

> See [Start building / Create endpoint](/start-building/2-create-endpoint)

Endpoint contains a way to call an API endpoint (endpoint can contain multiple functions to call API endpoint).

The main goal of an endpoint is to:

- 🚀 Build and get the request using `$this->api->X()`. See [Calling API section.](#calling-api)
- 🛠 Create a response from request result using `$this->makeResponse`

## Implementation

> Place your endpoints in `YourApi\Endpoints\MyEndpoint` namespace and use Endpoint suffix.

Implementation is easy. Just implement the **basePath** to return base URI, then create function that calls API endpoint.

Your function should accept data for building url and the request body content:
  - Arguments for the URI should be like **int $id** or and `Entity` object.
  - Options for the data should use `OptionsContract` and can be null or built (). See [Passing data section.](#passing-data)

```php
class UnitsEndpoint extends AbstractEndpoint
{
    protected function basePath(): string
    {
        return 'units';
    }

    /**
     * @throws InvalidJsonResponseException
     */
    public function get(
        int $id,
        GetUnitsOptions $options = null,
    ): UnitsResponse {
        $result = $this->api->post($this->uri()->addPath($id), $options);

        return $this->makeResponse(UnitsResponse::class, $result);
    }
}
```

<alert>

- You can change the base URI building by overriding **uri** implementation in your endpoint.
- You can use dependency injection in the **__construct**.

</alert>

## Calling API

In your endpoint use `$this->api->X()` to call API. 

Use `$this->uri()` to get endpoints base url. You can append query data as you please: 

- For adding query data use `$this->uri()->addQueryParam('key', 'myvalue')`
- Add path parameter using `$this->uri()->addPath($id)`. No need to add `/`.
- For more check [JustSteveKing/uri-builder](https://github.com/JustSteveKing/uri-builder).

### Passing data

For passing data you can use:

- `string`
- object that implements `StreamInterface`
- object that implements `OptionsContract` -> returns a string
- object that extends `AbstractJsonOptions` for sending **JSON** data

<alert>

We advise that you use `MyOptions $options = new MyOptions()` in your function when you want default values to be sent.

</alert>

#### Options

Options objects are ideal to for building data that can be sometimes optional. Helps to make type safe code.

1. Implement `OptionsContract`
2. Use `__constructor` for your setting properties that can be adjusted by the consumer.
3. Use `nullable` properties for optional data (with combination `array_filter`)
4. Implement `toBody` to build the data

#### JSON Options

For building JSON data you want to extend `AbstractJsonOptions` and implement `toArray`. 

<code-group>
  <code-block label="With default data" active>

  ```php
use WrkFlow\ApiSdkBuilder\Options\AbstractJsonOptions;

class PageInfoOptions extends AbstractJsonOptions
{
    public function __construct(
        public int $page = 1,
        public int|null $perPage = 100
    ) {
    }

    public function toArray(): array
    {
        return [
            'page_info' => [
                'page' => $this->page,
                'per_page' => $this->perPage,
            ],
        ];
    }
}
  ```

  </code-block>
  <code-block label="With required and optional data">

  ```php
use WrkFlow\ApiSdkBuilder\Options\AbstractJsonOptions;

class PageInfoOptions extends AbstractJsonOptions
{
    public function __construct(
        public int $page = 1,
        public int|null $perPage = null
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'page' => $this->page,
            'items_per_page' => $this->perPage,
        ]);
    }
}
  ```

  </code-block>
</code-group>


When using `AbstractJsonOptions` you can accept multiple options in you method and then merge them for API call using `MergedJsonOptions` (accepts null).

```php

/**
 * @throws InvalidJsonResponseException
 */
public function paginate(
    GetUnitsOptions $options = null,
    PageInfoOptions $page = new PageInfoOptions()
): UnitsResponse {
    $result = $this->api->post($this->uri(), new MergedJsonOptions([$options, $page]));

    return $this->makeResponse(UnitsResponse::class, $result);
}
```

### Get request

##### Arguments:

1. URI address to call `uri: JustSteveKing\UriBuilder\Uri`
2. Headers to send `headers: array<int|string,HeadersContract|string|string[]>`

```php
$response = $this->api->post($this->uri(), $options);
```

### Post request

#### Arguments:

1. URI address to call `uri: JustSteveKing\UriBuilder\Uri`
2. Data to send `options: OptionsContract|StreamInterface|string`
3. Headers to send `headers: array<int|string,HeadersContract|string|string[]>`

```php
$response = $this->api->post($this->uri(), $options);
```

### Put request

<alert type="warning">

To be implemented. Make PR! Edit `AbstractApi`.

</alert>

### Delete request

<alert type="warning">

To be implemented. Make PR! Edit `AbstractApi`.

</alert>

### Custom requests

You build any request you want by creating request using `$this->api->factory()->request(): PSR-7 compatible` and then
calling `sendRequest`.

```php
/**
 * @param array<int|string,HeadersContract|string|string[]> $headers
 */
public function delete(
    
): ResponseInterface {
    $request = $this->api->factory()
        ->request()
        ->createRequest('DELETE', $this->uri()->toString());

    return $this->api->sendRequest($request, $headers, $body);
}
```

<alert>

If the package misses some "general" requests, please make a PR and edit `AbstractApi`.

</alert>


## Conventions

### Methods

#### get

Use this for retrieving non paginated response. It can be post / get any HTTP method.

#### create

Create resource. It can be post / put any HTTP method.

#### update

Update resource It can be post / put any HTTP method.

#### delete

Delete resource. It can be post / put / delete any HTTP method.

#### paginate

Use this for endpoints that returns paginated results.

Ideal to combine with [AbstractJsonItemsResponse](/architecture/responses#abstractjsonitemsresponse)
and [PaginatedResponse](/architecture/responses#paginatedresponse) trait.

#### all

Use this if you want to provide a way to loop all entries via paginated API endpoint.

You should provide:

- Options for tweaking the request.
- Item callback.
- Callbacks for the loop lifecycle.

```php
public function all(
    Closure $onItem,
    GetUnitsOptions $options = null,
    ?Closure $onResponse = null,
    ?Closure $onLoopEnd = null,
): bool;
```

The implementation is currently not abstracted.

### Name

- Try to use the same structure and name as the api endpoint

### Folder structure

It is better to store endpoints in their own folder with all responses, options and entities in the folder.

```
Endpoints/UsersEndpoint/
    - UsersEndpoint.php
    - UsersResponse.php
    - UserEntity.php
    - GetUsersOptions.php
    - JsonToUserEntity.php (transformer)
```

#### Example

- from `/repos/{owner}/{repo}/actions/artifacts`
- to `GithubApi\Endpoints\Repos\Actions\Artifacts\ArtifactsEndpoint`


# Upgrade guide

## v0.1.2

- Moved `WrkFlow\ApiSdkBuilder\ApiFactory` to `WrkFlow\ApiSdkBuilder\Factories\ApiFactory`.
- Moved `WrkFlow\ApiSdkBuilder\Response` namespace to `WrkFlow\ApiSdkBuilder\Factories\Responses` namespace.
- `WrkFlow\ApiSdkBuilder\Contracts\OptionsContract` has new signature for `toBody(AbstractEnvironment $environment)`.
- `WrkFlow\ApiSdkBuilder\Options\AbstractJsonOptions`  has new signature for `toArray(AbstractEnvironment $environment)`.
- `WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract` has new signature `mixed $body` parameter for `makeResponse(string $class, ResponseInterface $response, mixed $body): AbstractResponse;`

### Response classes

Response classes that extends `AbstractJsonResponse/AbstractJsonItemsResponse` requires new parameter `array $body` in `__construct`.

It is **important** that name of the parameters is `$body`.

**Also** `parseJson` function is removed. Transfer your parsing code within constructor.

- `json` returns `GetValue` instance. 
- `xml` returns `GetValue` instance. [Docs](https://php-get-typed-value.wrk-flow.com).

### WorksWithJson / WorksWithXml / Transformers

Both traits were removed in favor of [GetValue package that makes accessing data easier](https://php-get-typed-value.wrk-flow.com).

### Endpoint

`makeResponse` has been removed. Update your endpoints to new usage:

```php
public function paginate(
    GetUnitsOptions $options = null,
    PageInfoOptions $page = new PageInfoOptions()
): UnitsResponse {
    $result = $this->api->post($this->uri(), new MergedJsonOptions([$options, $page]));

    return $this->makeResponse(UnitsResponse::class, $result);
} 
```

to 

```php
public function paginate(
    GetUnitsOptions $options = null,
    PageInfoOptions $page = new PageInfoOptions()
): UnitsResponse {
    return $this->api->post(
        responseClass: UnitsResponse::class,
        uri: $this->uri(), 
        body: new MergedJsonOptions([$options, $page]),
    );
} 
```

`__construct` signature has been changed - `MakeBodyFromResponseAction $makeBodyFromResponseAction` removed.

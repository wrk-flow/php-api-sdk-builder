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


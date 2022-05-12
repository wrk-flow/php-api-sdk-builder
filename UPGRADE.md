# Upgrade guide

## v0.1.2

- Moved `WrkFlow\ApiSdkBuilder\ApiFactory` to `WrkFlow\ApiSdkBuilder\Factories\ApiFactory`.
- Moved `WrkFlow\ApiSdkBuilder\Response` namespace to `WrkFlow\ApiSdkBuilder\Factories\Responses` namespace.
- `WrkFlow\ApiSdkBuilder\Contracts\OptionsContract` has new signature for `toBody(AbstractEnvironment $environment)`.
- `WrkFlow\ApiSdkBuilder\Options\AbstractJsonOptions`  has new signature for `toArray(AbstractEnvironment $environment)`.

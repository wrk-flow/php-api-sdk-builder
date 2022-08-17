---
title: Architecture and conventions
menuTitle: Architecture
subtitle: 'To properly create your API it is important to understand the conventions and the architecture 🚀'
category: Architecture
position: 11
---

API is built using these conventions:

- **API object:** provides the API interface
- **Environments:** provides user customization of the API
- **Endpoints:** provides a way to call API endpoint and return response
    - **Options:** provides ability to send data to the API endpoint
- **Response:** Holds the response data with defined "options".
    - **Entities:** Data transfer objects for responses.
    - **Transformers:** Transform classes that will transform any data (response array) to entity.

## API

> See [Start building / Usage](/start-building/create-api)

API is the main class that holds:

- which environment we should use,
- which endpoints are available
- defines base headers for the environment

### Conventions

- Always return the type of the endpoint.
- Use `MyEndpoint::class` syntax instead of strings.
- Do not alter `__construct`, use Environments for custom data.

#### Name

- If you are using DDD use `Api` namespace in your domain. Like `Github\Api` (for private usage)
- Name your class `GithubApi` (use Api suffix)
- For endpoints use `camelCase` naming.

### Example

```php
namespace MagellanoApi;

use MagellanoApi\Endpoints\Units\UnitsEndpoint;
use MagellanoApi\Endpoints\UnitsAvailabilities\UnitsAvailabilitiesEndpoint;
use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;

class MagellanoApi extends AbstractApi
{
    public function headers(): array
    {
        return [new JsonHeaders()];
    }

    public function units(): UnitsEndpoint
    {
        return $this->makeEndpoint(UnitsEndpoint::class);
    }

    public function unitsAvailabilities(): UnitsAvailabilitiesEndpoint
    {
        return $this->makeEndpoint(UnitsAvailabilitiesEndpoint::class);
    }
}
```

## ApiFactory

> See [Start building / Usage](/start-building/use-it)

Used for passing PSR implementations for HTTP/s communication and container factory (for dependency injection).

Factory is later used in your API to build endpoints, responses.

## Transformers

- Try to indicate within the name of class what is the input / output (like *UnitAvailabilityToEntity* or *
  JsonToUnitAvailability*).
- Always add `transform(IntType $object): OutType` function that will make the transformation.
- We do recommend implementing `GetValueTransformerContract` interface which then can be used with `*GetterTransformers`

```php
use use Wrkflow\GetValue\Transformers\ArrayItemGetterTransformer;

class JsonToUnitAvailabilityEntity implements GetValueTransformerContract
{
    public function transform(GetValue $value, string $key): UnitAvailabilityEntity
    {
        $id = $value->getRequiredInt('ID');
        $isAvailable = $value->getRequiredBool('isAvailable');

        $availabilityStates = $value->getArray('availabilityStatus', [
            new ArrayItemGetterTransformer(function (GetValue $value): UnitAvailabilityStateEntity {
                return new UnitAvailabilityStateEntity(
                    day: $value->getRequiredString('day'),
                    state: $value->getRequiredEnum('status', AvailabilityState::class),
                );
            }),
        ]);

        return new UnitAvailabilityEntity(
            id: $id,
            isAvailable: $isAvailable,
            availabilityStates: $availabilityStates
        );
    }
}
```

## Entities

Entities are Data transfer objects. We do place them in `Entities` namespace when it is used by more responses.
Otherwise, it is located in same folder as the endpoint.

The entity should be immutable.

```php
class UnitAvailabilityEntity
{
    /**
     * @param array<UnitAvailabilityStateEntity> $availabilityStates can be empty if hideDetails is true
     */
    public function __construct(
        public readonly int $id,
        public readonly bool $isAvailable,
        public readonly array $availabilityStates
    ) {
    }
}
```



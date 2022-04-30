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

- Try to indicate within the name of class what is the input / output.
- Always add `transform(IntType $object): OutType` function that will make the transformation.
- Extend `AbstractJsonTransformer` if you are converting array to entity object. Contains helper method from `WorksWithJson`

```php
use WrkFlow\ApiSdkBuilder\Transformers\AbstractJsonTransformer;

class JsonToUnitAvailabilityEntity extends AbstractJsonTransformer
{
    private const KEY_AVAILABILITY_STATUS = 'availabilityStatus';

    public function transform(array $item): UnitAvailabilityEntity
    {
        $id = $this->getInt($item, 'ID');
        $isAvailable = $this->getBool($item, 'isAvailable');

        $availabilityStates = [];
        if (array_key_exists(self::KEY_AVAILABILITY_STATUS, $item) === true
            && is_array($item[self::KEY_AVAILABILITY_STATUS]) === true) {
            foreach ($item[self::KEY_AVAILABILITY_STATUS] as $item) {
                $availabilityStates[] = new UnitAvailabilityStateEntity(
                    day: $item['day'],
                    state: AvailabilityState::from($item['status']),
                );
            }
        }

        return new UnitAvailabilityEntity(
            id: $id,
            isAvailable: $isAvailable,
            availabilityStates: $availabilityStates
        );
    }
}
```

## Entities

Entities are Data transfer objects. We do place them in `Entities` namespace when it is used by more responses. Otherwise, it is located in same folder as the endpoint. 

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

## Concerns (utils)

### WorksWithJson

Trait that allows you to access values from an array with type safe manner.

```php
/**
 * @param array<string, mixed> $data
 */
public function getInt(array $data, string $key): ?int;

/**
 * @param array<string, mixed> $data
 */
public function getFloat(array $data, string $key): ?float;

/**
 * @param array<string, mixed> $data
 */
public function getBool(array $data, string $key): ?bool;

public function floatVal(mixed $value): ?float;

public function intVal(mixed $value): ?int;

public function boolVal(mixed $value): ?bool;
```

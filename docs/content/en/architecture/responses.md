---
title: Responses
category: Architecture
position: 14
---

Response main job is to take the result data and convert it to an object.

You are responsible to validate the:

- status code of the response (should not raise exception).
- build the data and set it in response (can raise an exception).
- build an error entity if there is an error

Currently, we are not throwing exceptions. Use `isSuccesfull` to determine if there is an error. This allows
to work with the response with any state and adds more flexibility.

<alert>

This can probably change. Maybe raising exception is always the best option.

</alert>

## TODO

- A convention to return error entity state (nullable) if isSuccessful() is false

## Base implementation

1. Override **__construct** and try to build the response (parse json / xml / etc).
2. Create `private bool $isSuccessful = false;` and set it to true if the response is successful.
3. Implement `public function isSuccessful(): bool`
4. Create properties and entities for your data. Use **getters** instead of properties

### Consumer functions

These functions are provided for the consumer:

- `isSuccessful: bool` - Indicates if the response is successfully and the data can be accessed.
- `getResponse(): ResponseInterface` - Returns underlying response.

## JSON response

To help parse JSON responses extend `AbstractJsonResponse` that will try to parse response to json and check if keys are
present in an array.

- Raises `InvalidJsonResponseException` if the response is not json.
- Raises `InvalidJsonResponseException` if the of json value is invalid.
- Will not raise exception if any of desired keys is missing.

### Consumer functions

These functions are provided for the consumer:

- `toArray(): array` - Returns the json. Will raise exception if the data is not valid.

### Implementation

- Implement `parseJson(array $json): bool;` to parse the json. Return false if not valid. **I recommend to create
  properties and getters instead using toArray function**.
- You can use `$this->hasKeys(json: $json, keys: [...]): bool` to check if the array contains given keys.
- You can use [WorksWithJson](/architecture/utils#workswithjson) to easily get values from json with proper type.

## JSON response with items array

> For step by step implementation check  [Start building / create response](/start-building/create-response)

Use this if you want to provide a way to easily access array items in the json using transformer and base root keys
validation.

### Consumer functions

These functions are provided for the consumer

- `getRawItems` - Returns raw items array.
- `items` - Will return transformed data.
- `loopItems` - Will provide a way to loop transformed items using closure.

### Implementation

```php
/**
 * Class must use AbstractJsonTransformer base class.
 *
 * @return class-string<AbstractJsonTransformer>
 */
abstract protected function getTransformerClass(): string;

/**
 * Return keys that are required for parsing the response. You can return and empty data.
 *
 * @return array<string>
 */
abstract protected function requiredRootKeys(): array;

/**
 * Return key that holds the items (something like data, items) in the root json data. If you return null root json
 * will be used for items.
 */
abstract protected function itemsKey(): ?string;
```

Then implement `items` / `loopItems`. These methods are only to force you to set correct typehints.

```php
/**
 * @return array<UnitAvailabilityEntity>
 */
public function items(): array
{
    return $this->transformUsingArray();
}

/**
 * You will receive UnitAvailabilityEntity on each item. Returns false if items are empty. Faster
 * than looping items.
 *
 * @param Closure(UnitAvailabilityEntity) $onItem
 */
public function loopItems(Closure $onItem): bool
{
    return $this->transformUsingLoop($onItem);
}
```

## Provided abstractions

We have implemented some abstraction to help re using same logic.

### PaginatedResponse

Use this trait to expose methods for getting pagination.

```php
public function getItemsPerPage(): int
public function getTotalItems(): int
public function getCurrentPage(): int
public function getTotalPages(): int;
public function onLastPage(): bool;
```

**Implementation:**

You need to set these properties, otherwise PHP will crash whenever function above is called.

```php
protected int $itemsPerPage;
protected int $totalItems;
protected int $currentPage;
protected int $totalPages;
```

**Example:**

```php
class UnitsResponse extends AbstractJsonResponse
{
    use PaginatedResponse;
    
    protected const KEY_ITEMS = 'items';
    protected const KEY_PAGINATION = 'pagination';
    
    protected function parseJson(array $json): bool
    {
        if ($this->hasKeys([self::KEY_ITEMS]) === false) {
            return false;
        }

        // Can be null if items are empty
        $result = $json[self::KEY_PAGINATION];

        $this->totalItems = $result['totalItems'] ?? 0;
        $this->totalPages = $result['totalPages'] ?? 1;
        $this->itemsPerPage = $result['itemsPerPage'] ?? $this->totalItems;
        $this->currentPage = $result['page'] ?? 1;

        return true;
    }
}
```


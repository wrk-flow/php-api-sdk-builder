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

Currently, we are not throwing exceptions. Use `isSuccesfull` to determine if there is an error. 

<alert>

This can probably change. Maybe raising exception is always the best option.

</alert>

## TODO

- A convention to return error entity state (nullable) if isSuccessful() is false

## Base implementation

1. Override **__construct** and try to build the response
2. Create `private bool $isSuccessful = false;` and set it to true if the response is successful.
3. Create properties and entities for your data.

## Json responses

To help parse JSON responses extend `AbstractJsonResponse` that will try to parse response to json and check if keys are present in an array. 

- Raises `InvalidJsonResponseException` if the response is not json.
- Raises `InvalidJsonResponseException` if the of json value is invalid.
- Will not raise exception if any of desired keys is missing.
- Use `$this->hasKeys(json: $json, keys: [...]): bool` to check if the array contains given keys.
- Use `toArray` to get json data. Will raise exception if the data is not valid.

### AbstractJsonItemsResponse

> For step by step implementation check  [Start building / create response](/start-building/create-response)

When you want to provide a way to easily access array items in the json using transformer and base root keys validation

You can use:

- `getRawItems` - Returns raw items array
- `items` - Will return

#### Implementation

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


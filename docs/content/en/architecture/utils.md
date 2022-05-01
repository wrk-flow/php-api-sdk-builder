---
title: Utils
category: Architecture
subTitle: Tools for testing.
position: 17
---

## Concerns

Traits that helps you de-duplicate code.

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

<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Concerns;

use DateTimeInterface;
use WrkFlow\ApiSdkBuilder\Exceptions\ArrayIsEmptyException;
use WrkFlow\ApiSdkBuilder\Exceptions\MissingValueForKeyException;
use WrkFlow\ApiSdkBuilder\Exceptions\NotAnArrayException;

trait WorksWithJson
{
    use WorksWithValue;

    /**
     * @param array<string, mixed> $data
     */
    protected function getInt(array $data, string $key): ?int
    {
        return $this->intVal($data[$key] ?? null);
    }

    protected function getRequiredInt(array $data, string $key): int
    {
        $value = $this->getInt($data, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function getFloat(array $data, string $key): ?float
    {
        return $this->floatVal($data[$key] ?? null);
    }

    protected function getRequiredFloat(array $data, string $key): float
    {
        $value = $this->getFloat($data, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function getBool(array $data, string $key): ?bool
    {
        return $this->boolVal($data[$key] ?? null);
    }

    protected function getRequiredBool(array $data, string $key): bool
    {
        $value = $this->getBool($data, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    protected function getString(array $data, string $key): ?string
    {
        return $this->stringVal($data[$key] ?? null);
    }

    protected function getRequiredString(array $data, string $key): string
    {
        $value = $this->getString($data, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    protected function getDateTime(array $data, string $key): ?DateTimeInterface
    {
        return $this->dateTimeVal($data[$key] ?? null);
    }

    protected function getRequiredDateTime(array $data, string $key): DateTimeInterface
    {
        $value = $this->getDateTime($data, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    /**
     * Ensures that always array will be returned (if missing in $data or if null).
     */
    protected function getArray(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        if ($value === null) {
            return [];
        }

        if (is_array($value) === false) {
            throw new NotAnArrayException($key);
        }

        return $value;
    }

    /**
     * Checks if the array is in the data set with non-empty array
     *
     * @phpstan-return non-empty-array
     */
    protected function getRequiredArray(array $data, string $key): array
    {
        $value = $this->getArray($data, $key);

        if ($value === []) {
            throw new ArrayIsEmptyException($key);
        }

        return $value;
    }
}

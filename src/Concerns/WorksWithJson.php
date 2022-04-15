<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Concerns;

trait WorksWithJson
{
    /**
     * @param array<string, mixed>  $data
     */
    protected function getInt(array $data, string $key): ?int
    {
        return $this->intVal($data[$key] ?? null);
    }

    /**
     * @param array<string, mixed>  $data
     */
    protected function getFloat(array $data, string $key): ?float
    {
        return $this->floatVal($data[$key] ?? null);
    }

    /**
     * @param array<string, mixed>  $data
     */
    protected function getBool(array $data, string $key): ?bool
    {
        return $this->boolVal($data[$key] ?? null);
    }

    protected function floatVal(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        return floatval($value);
    }

    protected function intVal(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return (int) $value;
    }

    protected function boolVal(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return boolval($value);
    }
}

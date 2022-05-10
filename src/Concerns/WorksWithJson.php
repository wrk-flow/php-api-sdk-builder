<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Concerns;

trait WorksWithJson
{
    use WorksWithValue;

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
}

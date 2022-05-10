<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Concerns;

trait WorksWithValue
{
    protected function stringVal(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
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

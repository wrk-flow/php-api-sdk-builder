<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

interface HeadersInterface
{
    /**
     * Return key-ed headers (name => value) or another instance of header (no key).
     *
     * @return array<string|int, string|HeadersInterface|string[]>
     */
    public function headers(): array;
}

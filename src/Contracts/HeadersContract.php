<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

interface HeadersContract
{
    /**
     * Return key-ed headers (name => value) or another instance of header (no key).
     *
     * @return array<string|int, string|HeadersContract|string[]>
     */
    public function headers(): array;
}

<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

class AcceptsJsonHeaders implements HeadersInterface
{
    public function headers(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }
}

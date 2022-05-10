<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class AcceptsJsonHeaders implements HeadersContract
{
    public function headers(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }
}

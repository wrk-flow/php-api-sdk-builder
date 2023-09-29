<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

class XMLContentTypeHeaders implements HeadersInterface
{
    public function headers(): array
    {
        return [
            'Content-Type' => 'application/xml',
        ];
    }
}

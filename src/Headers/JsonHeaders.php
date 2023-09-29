<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

class JsonHeaders implements HeadersInterface
{
    public function headers(): array
    {
        return [new JsonContentTypeHeaders(), new AcceptsJsonHeaders()];
    }
}

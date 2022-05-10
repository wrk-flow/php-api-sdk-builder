<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class XMLHeaders implements HeadersContract
{
    public function headers(): array
    {
        return [new XMLContentTypeHeaders(), new AcceptsXMLHeaders()];
    }
}

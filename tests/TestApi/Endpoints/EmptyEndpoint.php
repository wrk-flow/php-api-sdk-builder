<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;

final class EmptyEndpoint extends AbstractEndpoint
{
    protected function basePath(): string
    {
        return 'empty';
    }
}

<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Endpoints;

use WrkFlow\ApiSdkBuilder\Contracts\ApiContract;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;

class EndpointMock extends AbstractEndpoint
{
    public function __construct(
        ApiContract $api,
        public readonly string $endpointClass
    ) {
        parent::__construct($api);
    }

    protected function basePath(): string
    {
        return 'endpoint';
    }
}

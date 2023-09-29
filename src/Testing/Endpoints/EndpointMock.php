<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Endpoints;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;

class EndpointMock extends AbstractEndpoint
{
    public function __construct(
        ApiInterface $api,
        public readonly string $endpointClass
    ) {
        parent::__construct($api);
    }

    protected function basePath(): string
    {
        return 'endpoint';
    }
}

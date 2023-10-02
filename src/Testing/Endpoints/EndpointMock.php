<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Endpoints;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;

class EndpointMock extends AbstractEndpoint
{
    public function __construct(
        EndpointDIEntity $di,
        public readonly string $endpointClass
    ) {
        parent::__construct($di);
    }

    protected function basePath(): string
    {
        return 'endpoint';
    }
}

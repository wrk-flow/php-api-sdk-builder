<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints;

use Http\Mock\Client;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;

abstract class AbstractTestEndpoint extends AbstractEndpoint
{
    protected Client $client;

    public function __construct(EndpointDIEntity $di)
    {
        parent::__construct($di);

        $client = $di
            ->api()
            ->factory()
            ->client();

        assert($client instanceof Client);

        $this->client = $client;
    }
}

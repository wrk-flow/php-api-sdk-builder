<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\PHPStan\Fixtures\Constructors;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;

final class BadEndpoint extends AbstractEndpoint
{
    public function __construct(EndpointDIEntity $dependencies)
    {
        parent::__construct($dependencies);
    }

    protected function basePath(): string
    {
        return '/fixture';
    }
}

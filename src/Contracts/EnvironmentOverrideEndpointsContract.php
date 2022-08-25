<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;

interface EnvironmentOverrideEndpointsContract
{
    /**
     * @return array<class-string<AbstractEndpoint>, class-string<AbstractEndpoint>>
     */
    public function endpoints(): array;
}

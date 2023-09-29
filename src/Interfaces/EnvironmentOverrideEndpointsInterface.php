<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;

interface EnvironmentOverrideEndpointsInterface
{
    /**
     * @return array<class-string, class-string<AbstractEndpoint>>
     */
    public function endpoints(): array;
}

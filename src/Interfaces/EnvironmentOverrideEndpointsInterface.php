<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

interface EnvironmentOverrideEndpointsInterface
{
    /**
     * @return array<class-string<EndpointInterface>, class-string<EndpointInterface>>
     */
    public function endpoints(): array;
}

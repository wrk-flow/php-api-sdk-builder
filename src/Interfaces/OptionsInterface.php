<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

interface OptionsInterface
{
    public function toBody(AbstractEnvironment $environment): string;
}

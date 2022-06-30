<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

interface OptionsContract
{
    public function toBody(AbstractEnvironment $environment): string;
}

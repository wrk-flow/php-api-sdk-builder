<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Options;

use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

abstract class AbstractJsonOptions implements OptionsContract
{
    abstract public function toArray(AbstractEnvironment $environment): array;

    public function toBody(AbstractEnvironment $environment): string
    {
        return json_encode($this->toArray($environment), JSON_THROW_ON_ERROR);
    }
}

<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Options;

use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;

abstract class AbstractJsonOptions implements OptionsInterface
{
    abstract public function toArray(AbstractEnvironment $environment): array;

    public function toBody(AbstractEnvironment $environment): string
    {
        return json_encode($this->toArray($environment), JSON_THROW_ON_ERROR);
    }
}

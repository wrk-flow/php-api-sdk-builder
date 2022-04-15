<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Options;

use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;

abstract class AbstractJsonOptions implements OptionsContract
{
    abstract public function toArray(): array;

    public function toBody(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}

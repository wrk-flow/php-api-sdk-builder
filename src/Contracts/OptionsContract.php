<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

interface OptionsContract
{
    public function toBody(): string;
}

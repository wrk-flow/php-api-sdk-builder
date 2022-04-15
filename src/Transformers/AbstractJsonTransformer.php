<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Transformers;

use WrkFlow\ApiSdkBuilder\Concerns\WorksWithJson;

abstract class AbstractJsonTransformer
{
    use WorksWithJson;

    abstract public function transform(array $item): object;
}

<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Transformers;

use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Concerns\WorksWithXML;
use Wrkflow\GetValue\GetValue;

abstract class AbstractXMLTransformer
{
    use WorksWithXML;

    abstract public function transform(GetValue $item): object|array|null;
}

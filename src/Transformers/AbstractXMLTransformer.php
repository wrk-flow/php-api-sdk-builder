<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Transformers;

use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Concerns\WorksWithXML;

abstract class AbstractXMLTransformer
{
    use WorksWithXML;

    abstract public function transform(SimpleXMLElement $item): object|array|null;
}

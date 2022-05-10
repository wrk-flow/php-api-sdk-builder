<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Concerns;

use SimpleXMLElement;

trait WorksWithXML
{
    use WorksWithValue;

    protected function getInt(SimpleXMLElement $xml, string $key): ?int
    {
        return $this->intVal($this->getValue($xml, $key));
    }

    protected function getFloat(SimpleXMLElement $xml, string $key): ?float
    {
        return $this->floatVal($this->getValue($xml, $key));
    }

    protected function getBool(SimpleXMLElement $xml, string $key): ?bool
    {
        return $this->boolVal($this->getValue($xml, $key));
    }

    protected function getString(SimpleXMLElement $xml, string $key): ?string
    {
        return $this->stringVal($this->getValue($xml, $key));
    }

    private function getValue(SimpleXMLElement $xml, string $key): string
    {
        $value = $xml[$key];

        return (string) $value;
    }
}

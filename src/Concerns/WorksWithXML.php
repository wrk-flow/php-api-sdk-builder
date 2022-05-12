<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Concerns;

use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Exceptions\MissingValueForKeyException;

trait WorksWithXML
{
    use WorksWithValue;

    protected function getInt(SimpleXMLElement $xml, string $key): ?int
    {
        return $this->intVal($this->getValue($xml, $key));
    }

    protected function getRequiredInt(SimpleXMLElement $xml, string $key): int
    {
        $value = $this->getInt($xml, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    protected function getFloat(SimpleXMLElement $xml, string $key): ?float
    {
        return $this->floatVal($this->getValue($xml, $key));
    }

    protected function getRequiredFloat(SimpleXMLElement $xml, string $key): float
    {
        $value = $this->getFloat($xml, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    protected function getBool(SimpleXMLElement $xml, string $key): ?bool
    {
        return $this->boolVal($this->getValue($xml, $key));
    }

    protected function getRequiredBool(SimpleXMLElement $xml, string $key): bool
    {
        $value = $this->getBool($xml, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    protected function getString(SimpleXMLElement $xml, string $key): ?string
    {
        return $this->stringVal($this->getValue($xml, $key));
    }

    protected function getRequiredString(SimpleXMLElement $xml, string $key): string
    {
        $value = $this->getString($xml, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    /**
     * @return \DateTime|\DateTimeImmutable|null
     */
    protected function getDateTime(SimpleXMLElement $xml, string $key): ?\DateTimeInterface
    {
        return $this->dateTimeVal($this->getValue($xml, $key));
    }

    /**
     * @return \DateTime|\DateTimeImmutable
     */
    protected function getRequiredDateTime(SimpleXMLElement $xml, string $key): \DateTimeInterface
    {
        $value = $this->getDateTime($xml, $key);

        if ($value === null) {
            throw new MissingValueForKeyException($key);
        }

        return $value;
    }

    private function getValue(SimpleXMLElement $xml, string $key): string
    {
        $value = $xml->{$key};

        return (string) $value;
    }
}

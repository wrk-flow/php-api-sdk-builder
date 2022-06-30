<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Options;

use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\InvalidOptionsXMLException;

abstract class AbstractXMLOptions implements OptionsContract
{
    public function toBody(AbstractEnvironment $environment): string
    {
        $contents = $this->buildXML($this->createRoot($environment))
            ->asXML();

        if (is_string($contents) === false) {
            throw new InvalidOptionsXMLException();
        }

        return $contents;
    }

    abstract protected function getRootNodeName(): string;

    abstract protected function getXMLSUrl(): string;

    abstract protected function buildXML(SimpleXMLElement $xml): SimpleXMLElement;

    protected function createRoot(AbstractEnvironment $environment): SimpleXMLElement
    {
        $xml = new SimpleXMLElement(sprintf('<%s />', $this->getRootNodeName()));
        $xml->addAttribute('xmlns', $this->getXMLSUrl());

        return $xml;
    }
}

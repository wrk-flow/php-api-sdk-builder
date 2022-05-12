<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

abstract class AbstractXMLResponse extends AbstractResponse
{
    protected SimpleXMLElement $xml;

    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);

        $this->xml = new SimpleXMLElement($response->getBody()->getContents());
    }
}

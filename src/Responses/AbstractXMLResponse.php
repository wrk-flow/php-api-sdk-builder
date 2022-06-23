<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Contracts\BodyIsXmlContract;

abstract class AbstractXMLResponse extends AbstractResponse implements BodyIsXmlContract
{
    public function __construct(
        ResponseInterface $response,
        protected SimpleXMLElement $xml
    ) {
        parent::__construct($response);
    }
}

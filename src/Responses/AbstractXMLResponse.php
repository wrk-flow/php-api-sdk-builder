<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\BodyIsXmlInterface;
use Wrkflow\GetValue\GetValue;

abstract class AbstractXMLResponse extends AbstractResponse implements BodyIsXmlInterface
{
    public function __construct(
        ResponseInterface $response,
        protected GetValue $body
    ) {
        parent::__construct($response);
    }

    public function body(): GetValue
    {
        return $this->body;
    }
}

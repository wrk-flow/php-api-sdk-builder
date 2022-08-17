<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\BodyIsXmlContract;
use Wrkflow\GetValue\GetValue;

abstract class AbstractXMLResponse extends AbstractResponse implements BodyIsXmlContract
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

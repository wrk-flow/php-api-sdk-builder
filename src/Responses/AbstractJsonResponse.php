<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\BodyIsJsonInterface;
use Wrkflow\GetValue\GetValue;

abstract class AbstractJsonResponse extends AbstractResponse implements BodyIsJsonInterface
{
    public function __construct(
        ResponseInterface $response,
        protected readonly GetValue $body
    ) {
        parent::__construct($response);
    }

    /**
     * Response must be successful to access json.
     */
    public function body(): GetValue
    {
        return $this->body;
    }
}

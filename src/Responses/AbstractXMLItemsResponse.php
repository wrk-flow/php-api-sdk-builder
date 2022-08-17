<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Responses\Concerns\ItemsTransform;
use Wrkflow\GetValue\GetValue;

abstract class AbstractXMLItemsResponse extends AbstractXMLResponse
{
    use ItemsTransform;

    public function __construct(
        ResponseInterface $response,
        GetValue $body,
        // It is important that "container" name is used for dependency injection.
        protected readonly SDKContainerFactoryContract $container
    ) {
        parent::__construct($response, $body);
    }

    public function body(): GetValue
    {
        return parent::body();
    }

    protected function container(): SDKContainerFactoryContract
    {
        return $this->container;
    }
}

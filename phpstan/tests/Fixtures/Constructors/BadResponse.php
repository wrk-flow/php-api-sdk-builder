<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\PHPStan\Fixtures\Constructors;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Responses\AbstractJsonResponse;
use Wrkflow\GetValue\GetValue;

final class BadResponse extends AbstractJsonResponse
{
    public function __construct(ResponseInterface $reply, GetValue $payload, SDKContainerFactoryContract $factory)
    {
        parent::__construct($reply, $payload);
    }
}

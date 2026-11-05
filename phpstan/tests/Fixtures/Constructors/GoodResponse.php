<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\PHPStan\Fixtures\Constructors;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Responses\AbstractJsonResponse;
use Wrkflow\GetValue\GetValue;

final class GoodResponse extends AbstractJsonResponse
{
    public function __construct(GetValue $body, ResponseInterface $response, SDKContainerFactoryContract $container)
    {
        parent::__construct($response, $body);
    }

    public function transform(GetValue $data): GetValue
    {
        return $data;
    }
}

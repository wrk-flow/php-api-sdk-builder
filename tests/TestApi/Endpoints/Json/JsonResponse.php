<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractJsonResponse;
use Wrkflow\GetValue\GetValue;

class JsonResponse extends AbstractJsonResponse
{
    final public const KeySuccess = 'success';

    public readonly bool $success;

    public function __construct(ResponseInterface $response, GetValue $body)
    {
        parent::__construct($response, $body);

        $this->success = $body->getRequiredBool(self::KeySuccess);
    }
}

<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Entities;

use WrkFlow\ApiSdkBuilder\Contracts\SendRequestActionContract;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;

final class EndpointDIEntity
{
    public function __construct(
        private readonly ApiInterface $api,
        private readonly SendRequestActionContract $sendRequestAction,
    ) {
    }

    public function api(): ApiInterface
    {
        return $this->api;
    }

    public function sendRequestAction(): SendRequestActionContract
    {
        return $this->sendRequestAction;
    }
}

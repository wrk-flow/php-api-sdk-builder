<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

interface ApiFactoryContract
{
    public function request(): RequestFactoryInterface;

    public function client(): ClientInterface;

    public function stream(): StreamFactoryInterface;

    public function container(): SDKContainerFactoryContract;
}

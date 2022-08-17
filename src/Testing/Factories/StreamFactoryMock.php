<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Factories;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Testing\Responses\StringStream;

class StreamFactoryMock implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return new StringStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new StringStream('file:' . $filename . ':mode' . $mode);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new StringStream('test_resource');
    }
}

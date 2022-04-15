<?php

declare(strict_types=1);

namespace Wrkflow\ApiSdkBuilder\Tests;

use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeaders;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;

class BuildHeadersTest extends TestCase
{
    public function testWithInnerHeaders(): void
    {
        $request = new Request('GET', 'test');
        $result = (new BuildHeaders())->execute([
            new JsonHeaders(),
            'test' => 'Test',
            'Content-type' => ['application/xml'],
        ], $request);

        $this->assertEquals([
            'Accept' => ['application/json'],
            'Content-type' => ['application/json', 'application/xml'],
            'test' => ['Test'],
        ], $result->getHeaders());
    }
}

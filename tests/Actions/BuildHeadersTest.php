<?php

declare(strict_types=1);

namespace Tests\Wrkflow\ApiSdkBuilder\Actions;

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
            'test' => 'Tests',
            'Content-type' => ['application/xml'],
        ], $request);

        $this->assertEquals([
            'Accept' => ['application/json'],
            'Content-type' => ['application/json', 'application/xml'],
            'test' => ['Tests'],
        ], $result->getHeaders());
    }
}

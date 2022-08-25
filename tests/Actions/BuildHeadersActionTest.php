<?php

declare(strict_types=1);

namespace Wrkflow\ApiSdkBuilderTests\Actions;

use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeadersAction;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;

class BuildHeadersActionTest extends TestCase
{
    public function testWithInnerHeaders(): void
    {
        $request = new Request('GET', 'test');
        $result = (new BuildHeadersAction())->execute([
            new JsonHeaders(),
            'test' => 'Tests',
            'Content-Type' => ['application/xml'],
        ], $request);

        $this->assertEquals([
            'Accept' => ['application/json'],
            'Content-Type' => ['application/json', 'application/xml'],
            'test' => ['Tests'],
        ], $result->getHeaders());
    }
}

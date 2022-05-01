<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Headers;

use Nyholm\Psr7\Request;
use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeaders;
use WrkFlow\ApiSdkBuilder\Headers\BearerTokenAuthorizationHeader;

class BearerTokenAuthorizationHeaderTest extends TestCase
{
    public function testHeaders(): void
    {
        $token = new BearerTokenAuthorizationHeader('test');
        $token2 = new BearerTokenAuthorizationHeader('yes');

        $request = new Request('GET', 'test');
        $result = (new BuildHeaders())->execute([$token, $token2], $request);

        $this->assertEquals([
            'Authorization' => ['Bearer test', 'Bearer yes'],
        ], $result->getHeaders());
    }
}

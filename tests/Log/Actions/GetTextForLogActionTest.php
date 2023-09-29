<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Log\Actions;

use Closure;
use Exception;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Log\Actions\GetTextForLogAction;

/**
 * @phpstan-type ClosureAlias Closure(static, GetTextForLogAction, RequestInterface):void
 */
class GetTextForLogActionTest extends TestCase
{
    /**
     * @return array<string|int, array{0: ClosureAlias}>
     */
    public function data(): array
    {
        return [
            'no response, no exception, no duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [OK] 000 example.com /api/v1/clients [0s]',
                    actual: $action->execute(request: $request, requestDurationInSeconds: 0),
                ),
            ],
            'no response, no exception, has duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [OK] 000 example.com /api/v1/clients [30s]',
                    actual: $action->execute(request: $request, requestDurationInSeconds: 30),
                ),
            ],
            'no response, has exception, no duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [FAILED] 000 example.com /api/v1/clients [0s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 0,
                        exception: new Exception(),
                    ),
                ),
            ],
            'no response, has exception, has duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [FAILED] 000 example.com /api/v1/clients [30s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 30,
                        exception: new Exception(),
                    ),
                ),
            ],
            'has response 200, no exception, no duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [OK] 200 example.com /api/v1/clients [0s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 0,
                        response: new Response(),
                    ),
                ),
            ],
            'has response 200, no exception, has duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [OK] 200 example.com /api/v1/clients [30s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 30,
                        response: new Response(),
                    ),
                ),
            ],
            'has response 200, has exception, no duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [FAILED] 200 example.com /api/v1/clients [0s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 0,
                        response: new Response(),
                        exception: new Exception(),
                    ),
                ),
            ],
            'has response 200, has exception, has duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [FAILED] 200 example.com /api/v1/clients [30s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 30,
                        response: new Response(),
                        exception: new Exception(),
                    ),
                ),
            ],
            'has response 300, no exception, no duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [OK] 300 example.com /api/v1/clients [0s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 0,
                        response: new Response(300),
                    ),
                ),
            ],
            'has response 300, no exception, has duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [OK] 300 example.com /api/v1/clients [30s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 30,
                        response: new Response(300),
                    ),
                ),
            ],
            'has response 300, has exception, no duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [FAILED] 300 example.com /api/v1/clients [0s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 0,
                        response: new Response(300),
                        exception: new Exception(),
                    ),
                ),
            ],
            'has response 300, has exception, has duration' => [
                static fn (self $self, GetTextForLogAction $action, RequestInterface $request) => $self->assertEquals(
                    expected: 'GET [FAILED] 300 example.com /api/v1/clients [30s]',
                    actual: $action->execute(
                        request: $request,
                        requestDurationInSeconds: 30,
                        response: new Response(300),
                        exception: new Exception(),
                    ),
                ),
            ],
        ];
    }


    /**
     * @param ClosureAlias $assert
     * @dataProvider data
     */
    public function test(Closure $assert): void
    {
        $action = new GetTextForLogAction();
        $request = new Request('GET', 'https://example.com/api/v1/clients');
        $assert($this, $action, $request);
    }
}

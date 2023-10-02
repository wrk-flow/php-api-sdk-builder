<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Testing\Factories;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SendRequestActionContract;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use WrkFlow\ApiSdkBuilder\Testing\Assertions\SendTestRequestActionAssert;
use WrkFlow\ApiSdkBuilder\Testing\Factories\EndpointDIEntityFactory;
use WrkFlow\ApiSdkBuilder\Testing\Factories\TestSDKContainerFactory;
use WrkFlow\ApiSdkBuilder\Testing\Responses\JsonResponseMock;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonEndpoint;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonResponse;
use Wrkflow\GetValue\DataHolders\ArrayData;
use Wrkflow\GetValue\GetValue;

/**
 * This tests ensures:
 * - PHPStan does not warn about the types used in constructor
 * - Implementation works.
 *
 * @phpstan-type MakeClosure Closure(static, TestSDKContainerFactory, EndpointDIEntity):void
 * @phpstan-type ContainerClosure Closure(static, TestSDKContainerFactory):void
 */
class TestSDKContainerFactoryTest extends TestCase
{
    /**
     * @return array<string|int, array{0: MakeClosure}>
     */
    public function dataMake(): array
    {
        $data = [
            JsonResponse::KeySuccess => true,
        ];
        return [
            'makeEndpoint' => [
                static fn (self $self, TestSDKContainerFactory $container, EndpointDIEntity $di) => $self->assertInstanceOf(
                    expected: JsonEndpoint::class,
                    actual: $container->makeEndpoint($di->api(), JsonEndpoint::class),
                ),
            ],
            'make' => [
                static fn (self $self, TestSDKContainerFactory $container, EndpointDIEntity $di) => $self->assertInstanceOf(
                    expected: SendTestRequestActionAssert::class,
                    actual: $container->make(SendRequestActionContract::class),
                ),
            ],
            'makeResponse' => [
                static fn (self $self, TestSDKContainerFactory $container, EndpointDIEntity $di) => $self->assertTrue(
                    condition: $container
                        ->makeResponse(
                            class: JsonResponse::class,
                            response: new JsonResponseMock($data),
                            body: new GetValue(new ArrayData($data)),
                        )
                        ->success,
                ),
            ],
        ];
    }

    /**
     * @param MakeClosure $assert
     *
     * @dataProvider dataMake
     */
    public function testMakeClosureSyntax(Closure $assert): void
    {
        $assert($this, $this->closureSyntax(), EndpointDIEntityFactory::make());
    }

    /**
     * @param MakeClosure $assert
     *
     * @dataProvider dataMake
     */
    public function testMakeObjectSyntax(Closure $assert): void
    {
        $assert($this, $this->objectSyntax(), EndpointDIEntityFactory::make());
    }

    /**
     * @return array<string|int, array{0: ContainerClosure}>
     */
    public function dataHas(): array
    {
        return [
            'SendRequestActionContract' => [
                static fn (self $self, TestSDKContainerFactory $container) => $self->assertTrue(
                    $container->has(SendRequestActionContract::class)
                ),
            ],
            'JsonEndpoint' => [
                static fn (self $self, TestSDKContainerFactory $container) => $self->assertTrue(
                    $container->has(JsonEndpoint::class)
                ),
            ],
            'JsonResponse' => [
                static fn (self $self, TestSDKContainerFactory $container) => $self->assertTrue(
                    $container->has(JsonResponse::class)
                ),
            ],
            'no found' => [
                static fn (self $self, TestSDKContainerFactory $container) => $self->assertFalse(
                    $container->has(AbstractResponse::class)
                ),
            ],
        ];
    }

    /**
     * @param ContainerClosure $assert
     *
     * @dataProvider dataHas
     */
    public function testHasClosureSyntax(Closure $assert): void
    {
        $assert($this, $this->closureSyntax());
    }

    /**
     * @param ContainerClosure $assert
     *
     * @dataProvider dataHas
     */
    public function testHasObjectSyntax(Closure $assert): void
    {
        $assert($this, $this->objectSyntax());
    }

    /**
     * @return array<string|int, array{0: MakeClosure}>
     */
    public function dataMakeFail(): array
    {
        return [
            'makeEndpoint' => [
                static function (self $self, TestSDKContainerFactory $container, EndpointDIEntity $di): void {
                    $container->makeEndpoint(api: $di->api(), endpointClass: AbstractEndpoint::class);
                },
            ],
            'make' => [
                static function (self $self, TestSDKContainerFactory $container, EndpointDIEntity $di): void {
                    $container->make(class: SendTestRequestActionAssert::class);
                },
            ],
            'makeResponse' => [
                static function (self $self, TestSDKContainerFactory $container, EndpointDIEntity $di): void {
                    $container
                        ->makeResponse(
                            class: AbstractResponse::class,
                            response: new JsonResponseMock([]),
                            body: new GetValue(new ArrayData([])),
                        );
                },
            ],
        ];
    }

    /**
     * @param MakeClosure $assert
     *
     * @dataProvider dataMakeFail
     */
    public function testMakeFailClosureSyntax(Closure $assert): void
    {
        $this->expectException(BindingResolutionException::class);
        $assert($this, $this->closureSyntax(), EndpointDIEntityFactory::make());
    }

    /**
     * @param MakeClosure $assert
     *
     * @dataProvider dataMakeFail
     */
    public function testMakeFailObjectSyntax(Closure $assert): void
    {
        $this->expectException(BindingResolutionException::class);
        $assert($this, $this->objectSyntax(), EndpointDIEntityFactory::make());
    }

    protected function closureSyntax(): TestSDKContainerFactory
    {
        return new TestSDKContainerFactory(
            makeBindings: [
                SendRequestActionContract::class => static fn () => new SendTestRequestActionAssert(),
            ],
            makeEndpointBindings: [
                JsonEndpoint::class => static fn (EndpointDIEntity $di) => new JsonEndpoint($di),
            ],
            makeResponseBindings: [
                JsonResponse::class => static fn (ResponseInterface $response, $body) => new JsonResponse(
                    response: $response,
                    body: $body,
                ),
            ]
        );
    }

    protected function objectSyntax(): TestSDKContainerFactory
    {
        return new TestSDKContainerFactory(
            // Only makeBindings supports object syntax
            makeBindings: [
                SendRequestActionContract::class => new SendTestRequestActionAssert(),
            ],
            makeEndpointBindings: [
                JsonEndpoint::class => static fn (EndpointDIEntity $di) => new JsonEndpoint($di),
            ],
            makeResponseBindings: [
                JsonResponse::class => static fn (ResponseInterface $response, $body) => new JsonResponse(
                    response: $response,
                    body: $body,
                ),
            ]
        );
    }
}

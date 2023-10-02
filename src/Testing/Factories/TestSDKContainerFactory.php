<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Factories;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use Wrkflow\GetValue\GetValue;

final class TestSDKContainerFactory implements SDKContainerFactoryContract
{
    /**
     * @param array<class-string<object>, object|Closure():(object|null)>                                                           $makeBindings
     *     A map of closures mapped to a class that should create the instance.
     * @param array<class-string<AbstractEndpoint>, Closure(EndpointDIEntity):(AbstractEndpoint|null)>                 $makeEndpointBindings
     *     A map of closures mapped to a class that should create the instance.
     * @param array<class-string<AbstractResponse>, Closure(ResponseInterface, ?GetValue):(AbstractResponse|null)> $makeResponseBindings A map of closures mapped to a class that should create the instance.
     */
    public function __construct(
        private readonly array $makeBindings = [],
        private readonly array $makeEndpointBindings = [],
        private readonly array $makeResponseBindings = [],
    ) {
    }

    public function makeEndpoint(ApiInterface $api, string $endpointClass): AbstractEndpoint
    {
        $result = $this->makeFrom(
            abstract: $endpointClass,
            bindings: $this->makeEndpointBindings,
            makeGiven: static fn (Closure $make) => $make(EndpointDIEntityFactory::make(api: $api)),
        );
        assert($result instanceof $endpointClass, 'Binding must be instance of ' . AbstractEndpoint::class);
        return $result;
    }

    public function make(string $class): mixed
    {
        $result = $this->makeFrom(
            abstract: $class,
            bindings: $this->makeBindings,
            makeGiven: static fn (Closure $make) => $make(),
        );
        assert($result instanceof $class, 'Binding must be instance of ' . $class);

        return $result;
    }

    public function has(string $classOrKey): bool
    {
        $bindings = [$this->makeBindings, $this->makeResponseBindings, $this->makeEndpointBindings];
        foreach ($bindings as $binding) {
            if (array_key_exists($classOrKey, $binding)) {
                return true;
            }
        }

        return false;
    }

    public function makeResponse(string $class, ResponseInterface $response, ?GetValue $body): AbstractResponse
    {
        $result = $this->makeFrom(
            abstract: $class,
            bindings: $this->makeResponseBindings,
            makeGiven: static fn (Closure $make) => $make($response, $body),
        );
        assert($result instanceof $class, 'Binding must be instance of ' . AbstractResponse::class);

        return $result;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>                 $abstract
     * @param array<class-string<T>, T|Closure> $bindings
     *
     * @return T
     */
    private function makeFrom(string $abstract, array $bindings, Closure $makeGiven): object
    {
        $make = $bindings[$abstract] ?? null;

        if ($make === null) {
            throw new BindingResolutionException('Binding not set ' . $abstract);
        }

        if ($make instanceof Closure === false) {
            assert($make instanceof $abstract, 'Binding must be instance of ' . $abstract);
            return $make;
        }

        $result = $makeGiven($make);

        if ($result === null) {
            throw new BindingResolutionException('Failed to resolve ' . $abstract);
        }

        assert($result instanceof $abstract, 'Binding must be instance of ' . $abstract);

        return $result;
    }
}

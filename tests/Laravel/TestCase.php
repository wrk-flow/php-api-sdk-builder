<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use WrkFlow\ApiSdkBuilder\Laravel\LaravelServiceProvider;

abstract class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelServiceProvider::class];
    }

    protected function app(): Application
    {
        assert($this->app instanceof Application, 'App not initialized');

        return $this->app;
    }

    /**
     * Get the available container instance.
     *
     * @template T of object
     *
     * @param class-string<T>   $class
     * @param array<int, mixed> $parameters
     *
     * @return T
     */
    protected function make(string $class, array $parameters = []): object
    {
        $instance = $this->app()
            ->make($class, $parameters);

        assert(assertion: $instance instanceof $class, description: 'Instance is not of type ' . $class);

        return $instance;
    }
}

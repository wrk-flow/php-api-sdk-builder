<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Frameworks;

use Illuminate\Contracts\Events\Dispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

class LaravelPsrEventDispatcher implements EventDispatcherInterface
{
    public function __construct(private readonly Dispatcher $dispatcher)
    {
    }

    public function dispatch(object $event): object
    {
        $this->dispatcher->dispatch($event);

        return $event;
    }
}

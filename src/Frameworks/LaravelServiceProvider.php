<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Frameworks;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Watchers\ClientRequestWatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use WrkFlow\ApiSdkBuilder\Actions\MakeApiFactory;
use WrkFlow\ApiSdkBuilder\Containers\LaravelContainerFactory;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;

class LaravelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->app->singleton(SDKContainerFactoryContract::class, LaravelContainerFactory::class);
        $this->app->singleton(ApiFactoryContract::class, function (Container $container): ApiFactoryContract {
            /** @var MakeApiFactory $makeApiFactory */
            $makeApiFactory = $container->make(MakeApiFactory::class);

            return $makeApiFactory->execute();
        });
        $this->app->singleton(LaravelPsrEventDispatcher::class, LaravelPsrEventDispatcher::class);

        $this->app->bind(EventDispatcherInterface::class, LaravelPsrEventDispatcher::class);

        $this->booted(function (): void {
            $this->passEventsToTelescopeHttpCatcher();
        });
    }

    protected function getEventDispatcher(): Dispatcher
    {
        return $this->app->get('events');
    }

    protected function passEventsToTelescopeHttpCatcher(): void
    {
        if (class_exists(ClientRequestWatcher::class) === false) {
            return;
        }

        $events = $this->getEventDispatcher();

        $events->listen(RequestConnectionFailedEvent::class, function (RequestConnectionFailedEvent $event): void {
            $events = $this->getEventDispatcher();

            $events->dispatch(new ConnectionFailed(new Request($event->request)));
        });

        $events->listen(RequestFailedEvent::class, function (RequestFailedEvent $event): void {
            $events = $this->getEventDispatcher();

            $newEvent = new ResponseReceived(new Request($event->request), new Response(
                $event->exception->getResponse()
            ));
            $events->dispatch($newEvent);
        });

        $events->listen(ResponseReceivedEvent::class, function (ResponseReceivedEvent $event): void {
            $events = $this->getEventDispatcher();

            $newEvent = new ResponseReceived(new Request($event->request), new Response(
                $event->response->getResponse()
            ));
            $events->dispatch($newEvent);
        });
    }
}

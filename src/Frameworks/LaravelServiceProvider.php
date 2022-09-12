<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Frameworks;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Watchers\ClientRequestWatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
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

    public function logContext(
        RequestInterface $request,
        float $requestDurationInSeconds,
        ?ResponseInterface $response = null,
        ?Exception $exception = null
    ): array {
        $maxContentSize = 10000;
        return array_filter([
            'exception' => $exception?->getMessage(),
            'request_url' => (string) $request->getUri(),
            'request_body' => substr((string) $request->getBody(), 0, $maxContentSize),
            'request_headers' => $request->getHeaders(),
            'request_duration' => $requestDurationInSeconds,
            'response_body' => $response !== null ? substr((string) $response->getBody(), 0, $maxContentSize) : null,
            'response_headers' => $response?->getHeaders(),
        ]);
    }

    protected function getEventDispatcher(): Dispatcher
    {
        return $this->app->get('events');
    }

    protected function passEventsToTelescopeHttpCatcher(): void
    {
        $hasTelescope = class_exists(ClientRequestWatcher::class);

        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $logger = $config->get('app.debug') === true
            ? $this->app->make(LoggerInterface::class)
            : null;

        if ($logger instanceof LoggerInterface === false && $hasTelescope === false) {
            return;
        }

        $events = $this->getEventDispatcher();

        $events->listen(RequestConnectionFailedEvent::class, function (RequestConnectionFailedEvent $event) use (
            $hasTelescope,
            $logger
        ): void {
            $events = $this->getEventDispatcher();

            if ($hasTelescope) {
                $events->dispatch(new ConnectionFailed(new Request($event->request)));
            }

            $logger?->debug(
                'Request response failed',
                $this->logContext(
                    request: $event->request,
                    requestDurationInSeconds: $event->requestDurationInSeconds,
                    exception: $event->exception
                )
            );
        });

        $events->listen(RequestFailedEvent::class, function (RequestFailedEvent $event) use (
            $hasTelescope,
            $logger
        ): void {
            $events = $this->getEventDispatcher();

            if ($hasTelescope) {
                $newEvent = new ResponseReceived(new Request($event->request), new Response(
                    $event->exception->getResponse()
                ));
                $events->dispatch($newEvent);
            }

            $logger?->debug(
                'Request response failed',
                $this->logContext(
                    $event->request,
                    $event->requestDurationInSeconds,
                    $event->exception->getResponse(),
                    $event->exception
                )
            );
        });

        $events->listen(ResponseReceivedEvent::class, function (ResponseReceivedEvent $event) use (
            $hasTelescope,
            $logger
        ): void {
            $events = $this->getEventDispatcher();

            if ($hasTelescope) {
                $newEvent = new ResponseReceived(new Request($event->request), new Response(
                    $event->response->getResponse()
                ));
                $events->dispatch($newEvent);
            }

            $logger?->debug(
                'Request response',
                $this->logContext($event->request, $event->requestDurationInSeconds, $event->response->getResponse())
            );
        });
    }
}

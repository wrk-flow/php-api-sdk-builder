<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Laravel;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Watchers\ClientRequestWatcher;
use League\Flysystem\FilesystemOperator;
use LogicException;
use Psr\EventDispatcher\EventDispatcherInterface;
use WrkFlow\ApiSdkBuilder\Actions\MakeApiFactory;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Laravel\Commands\ClearFileLogsCommand;
use WrkFlow\ApiSdkBuilder\Laravel\Configs\ApiSdkConfig;
use WrkFlow\ApiSdkBuilder\Log\Actions\BuildRequestHttpFileAction;
use WrkFlow\ApiSdkBuilder\Log\Actions\ClearFileLogsAction;
use WrkFlow\ApiSdkBuilder\Log\Contracts\BuildRequestLogFileActionContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\DebugLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLogPathServiceContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\InfoLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\InfoOrFailFileLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Loggers\DebugLogger;
use WrkFlow\ApiSdkBuilder\Log\Loggers\FileLogger;
use WrkFlow\ApiSdkBuilder\Log\Loggers\InfoLogger;
use WrkFlow\ApiSdkBuilder\Log\Loggers\InfoOrFailFileLogger;
use WrkFlow\ApiSdkBuilder\Log\Services\FileLogPathService;

final class LaravelServiceProvider extends ServiceProvider
{
    public const KeyFilesystemOperator = 'api_sdk_filesystem_operator';
    private const ConfigPath = __DIR__ . '/Configs/api_sdk.php';
    private const ConfigKey = 'api_sdk';

    public function register(): void
    {
        parent::register();

        $this->bindApi();
        $this->bindEvents();
        $this->bindLogs();

        if ($this->app->runningInConsole()) {
            $this->commands([ClearFileLogsCommand::class]);
        }
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(path: self::ConfigPath, key: self::ConfigKey);

        $config = self::getConfig($this->app);
        $this->passEventsToTelescopeHttpCatcher($config);

        $clearAt = $config->getTimeForClearSchedule();

        if ($clearAt !== null) {
            $schedule = $this->app->make(Schedule::class);
            assert($schedule instanceof Schedule);

            $schedule->command(ClearFileLogsCommand::class)
                ->dailyAt($clearAt);
        }
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     *
     * @return T
     */
    protected static function make(Container $container, string $class): object
    {
        $object = $container->make($class);
        assert($object instanceof $class);
        return $object;
    }

    protected static function getFileSystemOperator(Container $container): FilesystemOperator
    {
        $fileManager = $container->make(FilesystemManager::class);
        assert($fileManager instanceof FilesystemManager);

        $disk = $fileManager->disk();
        if ($disk instanceof FilesystemAdapter === false) {
            throw new LogicException(
                'FilesystemManager::disk() must return an instance of FilesystemAdapter for FileLogger to work'
            );
        }

        return $disk->getDriver();
    }

    private function getConfig(Container $container): ApiSdkConfig
    {
        $config = self::make(container: $container, class: ApiSdkConfig::class);
        assert($config instanceof ApiSdkConfig);
        return $config;
    }

    private function bindEvents(): void
    {
        $this->app->singleton(abstract: LaravelPsrEventDispatcher::class, concrete: LaravelPsrEventDispatcher::class);
        $this->app->bind(abstract: EventDispatcherInterface::class, concrete: LaravelPsrEventDispatcher::class);
    }

    private function bindApi(): void
    {
        $this->app->singleton(abstract: SDKContainerFactoryContract::class, concrete: LaravelContainerFactory::class);

        $this->app->bind(LoggerConfigEntity::class, static function (Container $container): LoggerConfigEntity {
            $apiConfig = self::getConfig($container);

            return new LoggerConfigEntity(
                logger: $apiConfig->getLogging(),
                loggersMap: $apiConfig->getLoggers(),
                fileBaseDir: $apiConfig->getLogFileBaseDirectory(),
                keepLogFilesForDays: $apiConfig->getKeepLogFilesForDays(),
            );
        });
        $this->app->singleton(
            abstract: ApiFactoryContract::class,
            concrete: static function (Container $container): ApiFactoryContract {
                $makeApiFactory = self::make(container: $container, class: MakeApiFactory::class);
                $loggerConfig = self::make(container: $container, class: LoggerConfigEntity::class);

                return $makeApiFactory->execute(loggerConfig: $loggerConfig);
            }
        );
    }

    private function bindLogs(): void
    {
        $this->app->singleton(
            abstract: BuildRequestLogFileActionContract::class,
            concrete: BuildRequestHttpFileAction::class
        );
        $this->app->singleton(abstract: ClearFileLogsAction::class, concrete: ClearFileLogsAction::class);
        $this->app->singleton(abstract: FileLogPathServiceContract::class, concrete: FileLogPathService::class);
        $this->app->singleton(abstract: DebugLoggerContract::class, concrete: DebugLogger::class);
        $this->app->singleton(abstract: FileLoggerContract::class, concrete: FileLogger::class);
        $this->app->singleton(abstract: InfoLoggerContract::class, concrete: InfoLogger::class);
        $this->app->singleton(abstract: InfoOrFailFileLoggerContract::class, concrete: InfoOrFailFileLogger::class);

        // Not sure if we provided FilesystemOperator to whole Laravel application
        // if it would break some package usage... So lets be explicit.
        $this->app->bind(
            abstract: self::KeyFilesystemOperator,
            concrete: static fn (Container $container) => self::getFileSystemOperator($container)
        );
        $this->app
            ->when(FileLogger::class)
            ->needs(FilesystemOperator::class)
            ->give(self::KeyFilesystemOperator);

        $this->app
            ->when(FileLogger::class)
            ->needs(FilesystemOperator::class)
            ->give(self::KeyFilesystemOperator);

        $this->app
            ->when(ClearFileLogsAction::class)
            ->needs(FilesystemOperator::class)
            ->give(self::KeyFilesystemOperator);
    }

    private function getEventDispatcher(): Dispatcher
    {
        $events = $this->app->get('events');
        assert($events instanceof Dispatcher);
        return $events;
    }

    private function passEventsToTelescopeHttpCatcher(ApiSdkConfig $config): void
    {
        if ($config->isTelescopeEnabled() === false) {
            return;
        }

        if (class_exists(ClientRequestWatcher::class) === false) {
            return;
        }

        $events = $this->getEventDispatcher();

        $events->listen(
            events: RequestConnectionFailedEvent::class,
            listener: function (RequestConnectionFailedEvent $event): void {
                $events = $this->getEventDispatcher();

                $events->dispatch(new ConnectionFailed(new Request($event->request)));
            }
        );

        $events->listen(
            events: RequestFailedEvent::class,
            listener: function (RequestFailedEvent $event): void {
                $events = $this->getEventDispatcher();

                $newEvent = new ResponseReceived(new Request($event->request), new Response(
                    $event->exception->getResponse()
                ));
                $events->dispatch($newEvent);
            }
        );

        $events->listen(
            events: ResponseReceivedEvent::class,
            listener: function (ResponseReceivedEvent $event): void {
                $events = $this->getEventDispatcher();

                $newEvent = new ResponseReceived(new Request($event->request), new Response(
                    $event->response->getResponse()
                ));
                $events->dispatch($newEvent);
            }
        );
    }
}

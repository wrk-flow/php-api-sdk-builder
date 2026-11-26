<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Event;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Actions\MakeApiFactory;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SendRequestActionContract;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Http\BufferedResponseStream;
use WrkFlow\ApiSdkBuilder\Http\ResponseBufferMiddleware;
use WrkFlow\ApiSdkBuilder\Interfaces\BodyIsStreamedXmlInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\BuffersResponsesInterface;
use WrkFlow\ApiSdkBuilder\Log\Actions\ClearFileLogsAction;
use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggersMapEntity;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;
use WrkFlow\ApiSdkBuilder\Log\Loggers\FileLogger;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use WrkFlow\ApiSdkBuilder\Testing\Environments\TestingEnvironment;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonResponse;
use WrkFlow\ApiSdkBuilderTests\TestApi\TestApi;

class LaravelServiceProviderTest extends TestCase
{
    public function testLoggerCanBeBuilt(): void
    {
        foreach (LoggerConstants::DefaultLoggersMap as $loggers) {
            foreach ($loggers as $logger) {
                $this->assertInstanceOf(ApiLoggerInterface::class, $this->app()->make($logger));
            }
        }
    }

    public function testClearFilesLogsCommandRegistered(): void
    {
        $schedule = $this->app()
            ->make(Schedule::class);
        assert($schedule instanceof Schedule);

        $commands = [
            'api-sdk:logs:clear' => false,
        ];

        foreach ($schedule->events() as $event) {
            if ($event->command === null) {
                continue;
            }

            foreach (array_keys($commands) as $command) {
                if (str_contains($event->command, $command)) {
                    $commands[$command] = true;
                }
            }
        }

        foreach ($commands as $command => $found) {
            $this->assertTrue($found, sprintf('Command %s not found in schedule', $command));
        }
    }

    public function testLoggerConfigEntity(): void
    {
        $logger = $this->make(LoggerConfigEntity::class);

        $this->assertEquals(
            new LoggerConfigEntity(
                logger: '*:info_file',
                loggersMap: new LoggersMapEntity(LoggerConstants::DefaultLoggersMap),
                fileBaseDir: 'requests',
                keepLogFilesForDays: 14,
            ),
            $logger,
        );
    }

    public function testResponseBufferCleanupIsScheduledHourly(): void
    {
        $schedule = $this->make(Schedule::class);
        $commands = [];
        foreach ($schedule->events() as $event) {
            if ($event->command !== null && str_contains($event->command, 'api-sdk:response-buffers:clear')) {
                $commands[] = $event->expression;
            }
        }

        self::assertSame(['0 * * * *'], $commands);
        self::assertInstanceOf(Client::class, $this->make(ApiFactoryContract::class)->client());
    }

    public function testOnlyTheOptedInSdkUsesTheCustomSink(): void
    {
        $usesSdkSink = [];
        $stack = HandlerStack::create(static function (RequestInterface $request, array $options) use (&$usesSdkSink): PromiseInterface {
            self::assertSame('payload', (string) $request->getBody());
            self::assertSame('test', $request->getHeaderLine('X-Transport'));
            $usesSdkSink[] = ($options['sink'] ?? null) instanceof BufferedResponseStream;

            return Create::promiseFor(new Response(200, [], '{"success":true}'));
        });
        $stack->push($this->make(ResponseBufferMiddleware::class));
        $this->app()->instance(ClientInterface::class, new Client([
            'handler' => $stack,
            'headers' => [
                'X-Transport' => 'test',
            ],
        ]));
        $factory = $this->make(MakeApiFactory::class)->execute();
        $environment = new TestingEnvironment();
        $ordinary = new TestApi($environment, $factory);
        $buffered = new class($environment, $factory) extends TestApi implements BuffersResponsesInterface {
        };
        $send = $this->make(SendRequestActionContract::class);

        foreach ([$ordinary, $buffered, $ordinary] as $api) {
            $send->execute($api, new Request('POST', 'https://example.test', [], 'payload'), JsonResponse::class);
        }

        self::assertSame([false, true, false], $usesSdkSink);
    }

    public function testFilesystemOperatorIsInjectable(): void
    {
        $this->assertInstanceOf(FileLogger::class, $this->app()->make(FileLogger::class));
        $this->assertInstanceOf(
            ClearFileLogsAction::class,
            $this->app()
                ->make(ClearFileLogsAction::class),
        );
    }

    public function testMakeApiFactoryResolvesTheClientOnEachCall(): void
    {
        $this->app()->bind(ClientInterface::class, Client::class);
        $makeApiFactory = $this->make(MakeApiFactory::class);

        self::assertNotSame($makeApiFactory->execute()->client(), $makeApiFactory->execute()->client());
    }

    public function testTelescopeReceivesHeadersAndStatusForLargeFailuresAndStreamedResponses(): void
    {
        $events = Event::fake([ResponseReceived::class]);
        $request = new Request('GET', 'https://example.test');
        $headers = [
            'Content-Type' => 'application/xml',
            'X-Request-Id' => 'provider-id',
        ];
        $large = new Response(502, $headers, str_repeat('x', 2 * 1024 * 1024 + 1));
        $large->getBody()->seek(1);
        $streamed = new Response(200, $headers, '<Response/>');
        $streamedResponse = new class($streamed) extends AbstractResponse implements BodyIsStreamedXmlInterface {
        };
        $small = new Response(400, $headers, '<Error/>');
        $unknown = new Response(503, $headers, FnStream::decorate(Utils::streamFor('<Unknown/>'), [
            'getSize' => static fn (): ?int => null,
        ]));

        $events->dispatch(new RequestFailedEvent('large', $request, new ResponseException($large), 0.1));
        $events->dispatch(new ResponseReceivedEvent('streamed', $request, $streamedResponse, 0.1));
        $events->dispatch(new RequestFailedEvent('small', $request, new ResponseException($small), 0.1));
        $events->dispatch(new RequestFailedEvent('unknown', $request, new ResponseException($unknown), 0.1));

        $events->assertDispatchedTimes(ResponseReceived::class, 4);
        foreach ([$large, $streamed, $unknown] as $original) {
            $events->assertDispatched(ResponseReceived::class, static fn(ResponseReceived $event): bool => $event->response->status() === $original->getStatusCode()
                && $event->response->toPsrResponse()->getHeaders() === $original->getHeaders()
                && $event->response->body() === '');
        }

        $events->assertDispatched(
            ResponseReceived::class,
            static fn (ResponseReceived $event): bool => $event->response->status() === 400 && $event->response->body() === '<Error/>',
        );

        self::assertSame(1, $large->getBody()->tell());
        self::assertSame('x', $large->getBody()->read(1));
        self::assertSame('<Response/>', $streamed->getBody()->getContents());
        self::assertSame('<Unknown/>', $unknown->getBody()->getContents());
    }
}

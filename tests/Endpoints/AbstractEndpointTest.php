<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Endpoints;

use Closure;
use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Exceptions\ApiException;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLoggerContract;
use WrkFlow\ApiSdkBuilder\Testing\Exceptions\TestRequestSentException;
use WrkFlow\ApiSdkBuilder\Testing\Factories\EndpointDIEntityFactory;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonEndpoint;

final class AbstractEndpointTest extends TestCase
{
    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public function dataDontReportToExceptionsToFile(): array
    {
        return [
            'returns empty array if not set' => [
                static fn (self $self) => $self->assertTestShouldIgnoreLoggers(
                    assert: new TestShouldIgnoreLoggersSendRequestActionAssert(
                        testException: new ApiException(),
                        expectedIgnoreLoggers: [],
                    ),
                    onEndpoint: static fn (JsonEndpoint $endpoint) => $endpoint,
                ),
            ],
            'returns empty array if exception does not match' => [
                static fn (self $self) => $self->assertTestShouldIgnoreLoggers(
                    assert: new TestShouldIgnoreLoggersSendRequestActionAssert(
                        testException: new ApiException(),
                        expectedIgnoreLoggers: [],
                    ),
                    onEndpoint: static fn (JsonEndpoint $endpoint) => $endpoint
                        ->dontReportExceptionsToFile(exceptions: [TestRequestSentException::class]),
                ),
            ],
            'returns empty array if exception matches' => [
                static fn (self $self) => $self->assertTestShouldIgnoreLoggers(
                    assert: new TestShouldIgnoreLoggersSendRequestActionAssert(
                        testException: new ApiException(),
                        expectedIgnoreLoggers: [FileLoggerContract::class],
                    ),
                    onEndpoint: static fn (JsonEndpoint $endpoint) => $endpoint
                        ->dontReportExceptionsToFile(exceptions: [ApiException::class]),
                ),
            ],
        ];
    }


    /**
     * @param Closure(static):void $assert
     *
     * @dataProvider dataDontReportToExceptionsToFile
     */
    public function testDontReportToExceptionsToFile(Closure $assert): void
    {
        $assert($this);
    }

    public function assertTestShouldIgnoreLoggers(
        TestShouldIgnoreLoggersSendRequestActionAssert $assert,
        Closure $onEndpoint,
    ): void {
        $this->expectException(TestRequestSentException::class);

        $endpoint = new JsonEndpoint(di: EndpointDIEntityFactory::make(sendAssert: $assert));
        ($onEndpoint($endpoint))
            ->success();
    }
}

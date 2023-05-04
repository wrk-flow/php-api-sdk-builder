<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Log\Services;

use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Services\FileLogPathService;

class FileLogPathServiceTest extends TestCase
{
    private FileLogPathService $fileLogPathService;

    protected function setUp(): void
    {
        $this->fileLogPathService = new FileLogPathService();
    }

    public function dataIsRootDirectory(): array
    {
        return [
            ['2023-03-20', true],
            ['2022-12-10', true],
            ['2022-12-01', true],
            ['0000-00-00', true],
            ['2022-12-', false],
            ['2022-12-204', false],
            ['test', false],
            ['my-directory', false],
            ['00-00-00', false],
            ['01', false],
            ['', false],
        ];
    }


    /**
     * @dataProvider dataIsRootDirectory
     */
    public function testIsRootDirectory(string $directory, bool $expected): void
    {
        $this->assertEquals(expected: $expected, actual: $this->fileLogPathService->isRootDirectory($directory));
    }

    public function dataGetRootDirectoryName(): array
    {
        return [
            ['2022-12-10T00:30:00-03:00', '2022-12-10'],
            ['2022-12-10T00:30:00+03:00', '2022-12-09'],
            ['2022-12-10T00:30:00+00:00', '2022-12-10'],
            ['2022-12-10T23:30:00+01:00', '2022-12-10'],
            ['2022-12-10T23:30:00+02:00', '2022-12-10'],
            ['2022-12-10T23:30:00+03:00', '2022-12-10'],
            ['2022-12-10T23:30:00+04:00', '2022-12-10'],
            ['2022-12-10T23:30:00-01:00', '2022-12-11'],
            ['2022-12-10T23:30:00-02:00', '2022-12-11'],
            ['2022-12-10T23:30:00-03:00', '2022-12-11'],
            ['2022-12-10T23:30:00-04:00', '2022-12-11'],
        ];
    }


    /**
     * @dataProvider dataGetRootDirectoryName
     */
    public function testGetRootDirectoryName(string $date, string $expected): void
    {
        date_default_timezone_set('Europe/Prague');

        $this->assertEquals(
            expected: $expected,
            actual: $this->fileLogPathService->getRootDirectoryName(new DateTimeImmutable($date))
        );
    }

    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public function dataGetRootDirectoriesMapToNotDeleteDataProvider(): array
    {
        return [
            'keepLogFilesForDays 0 - leave today' => [
                static fn (self $self) => $self->assertGetRootDirectoriesMapToNotDeleteDataProvider(
                    config: new LoggerConfigEntity(keepLogFilesForDays: 0),
                    date: new DateTime('2020-01-01'),
                    expected: [
                        '2020-01-01' => true,
                    ],
                ),
            ],
            'keepLogFilesForDays 1' => [
                static fn (self $self) => $self->assertGetRootDirectoriesMapToNotDeleteDataProvider(
                    config: new LoggerConfigEntity(keepLogFilesForDays: 1),
                    date: new DateTime('2020-01-01'),
                    expected: [
                        '2020-01-01' => true,
                        '2019-12-31' => true,
                    ],
                ),
            ],
            'keepLogFilesForDays 2' => [
                static fn (self $self) => $self->assertGetRootDirectoriesMapToNotDeleteDataProvider(
                    config: new LoggerConfigEntity(keepLogFilesForDays: 2),
                    date: new DateTime('2020-01-01'),
                    expected: [
                        '2020-01-01' => true,
                        '2019-12-31' => true,
                        '2019-12-30' => true,
                    ],
                ),
            ],
            'keepLogFilesForDays 7' => [
                static fn (self $self) => $self->assertGetRootDirectoriesMapToNotDeleteDataProvider(
                    config: new LoggerConfigEntity(keepLogFilesForDays: 7),
                    date: new DateTime('2020-01-01'),
                    expected: [
                        '2020-01-01' => true,
                        '2019-12-31' => true,
                        '2019-12-30' => true,
                        '2019-12-29' => true,
                        '2019-12-28' => true,
                        '2019-12-27' => true,
                        '2019-12-26' => true,
                        '2019-12-25' => true,
                    ],
                ),
            ],
        ];
    }

    /**
     * @param Closure(static):void $assert
     *
     * @dataProvider dataGetRootDirectoriesMapToNotDeleteDataProvider
     */
    public function testGetRootDirectoriesMapToNotDeleteDataProvider(Closure $assert): void
    {
        $assert($this);
    }

    /**
     * @param array<string, bool> $expected
     */
    public function assertGetRootDirectoriesMapToNotDeleteDataProvider(
        LoggerConfigEntity $config,
        DateTimeInterface $date,
        array $expected
    ): void {
        $actual = $this->fileLogPathService->getRootDirectoriesMapToNotDelete($config, $date);
        $this->assertEquals($expected, $actual);
    }
}

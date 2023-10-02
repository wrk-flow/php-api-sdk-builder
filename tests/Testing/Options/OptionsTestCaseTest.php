<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Testing\Options;

use Closure;
use WrkFlow\ApiSdkBuilder\Testing\Options\OptionsTestCase;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonOptions;

class OptionsTestCaseTest extends OptionsTestCase
{
    private const KeyInput = 'input';
    private const KeyKeys = 'keys';

    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public function data(): array
    {
        return [
            'all values' => [
                static fn (self $self) => $self->assertOptions(
                    input: new JsonOptions(input: 'test', keys: [1, 2, 3]),
                    expected: [
                        self::KeyInput => 'test',
                        self::KeyKeys => [1, 2, 3],
                    ]
                ),
            ],
            'empty keys' => [
                static fn (self $self) => $self->assertOptions(
                    input: new JsonOptions(input: 'test 22', keys: []),
                    expected: [
                        self::KeyInput => 'test 22',
                    ]
                ),
            ],
            'empty values' => [
                static fn (self $self) => $self->assertOptions(
                    input: new JsonOptions(input: '', keys: []),
                    expected: []
                ),
            ],
        ];
    }


    /**
     * @param Closure(static):void $assert
     *
     * @dataProvider data
     */
    public function test(Closure $assert): void
    {
        $assert($this);
    }
}

<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Options;

use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Testing\Environments\TestingEnvironment;

abstract class OptionsTestCase extends TestCase
{
    protected function assertOptions(OptionsInterface $input, array|string $expected): void
    {
        $body = $input->toBody(new TestingEnvironment());
        if (is_array($expected)) {
            $body = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        }
        $this->assertEquals($expected, $body);
    }
}

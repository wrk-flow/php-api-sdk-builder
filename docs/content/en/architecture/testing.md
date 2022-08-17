---
title: Testing
category: Architecture
subTitle: Tools for testing.
position: 16
---

We do like unit test. For this we have provided basic test cases using `Mockery` and `PHPUnit`.

Our goals are to lower `Mockery` usage to minimum. For this we have implemented own mocks:

- `ApiFactoryMock`
- `ApiMock`
- `EndpointMock`
- `EndpointResponseMock`
- `TestingEnvironmentMock`

Thanks to this our testing code conforms to real code changes.

## Endpoint

- Extend `WrkFlow\ApiSdkBuilder\Testing\Endpoints\EndpointExpectation` 

### Example

```php
class UnitsAvailabilitiesEndpointTest extends EndpointTestCase
{
    public function testGet(): void
    {
        $options = new GetAvailabilityOptions(
            ids: [1],
            from: '2022-01-01',
            to: '2022-01-05'
        );

        $this->assertPost(
            new EndpointExpectation(
                UnitsAvailabilitiesResponse::class,
                '/unitsavailabilities',
                $options,
            ),
            fn () => (new UnitsAvailabilitiesEndpoint($this->api))->get($options)
        );
    }
}
```

## Responses

- Extend `WrkFlow\ApiSdkBuilder\Testing\Responses\ResponseTestCase`
- Create your response. If you need container you can use `$this->container` and add container make expectation `$this->expectContainerMake
```php
protected function createResponse(array $responseData): UnitsAvailabilitiesResponse
{
    $this->expectContainerMake(new JsonToUnitAvailabilityEntity());

    return new UnitsAvailabilitiesResponse(
        new ResponseMock(),
        new GetValue(new ArrayData($responseData)),
        $this->container
    );
} 
```
- Run your assets on the response

```php
<?php

declare(strict_types=1);

use WrkFlow\ApiSdkBuilder\Exceptions\InvalidJsonResponseException;
use WrkFlow\ApiSdkBuilder\Testing\Responses\ResponseMock;
use WrkFlow\ApiSdkBuilder\Testing\Responses\ResponseTestCase;
use Wrkflow\GetValue\DataHolders\ArrayData;
use Wrkflow\GetValue\Exceptions\MissingValueForKeyException;
use Wrkflow\GetValue\GetValue;

class UnitsAvailabilitiesResponseTest extends ResponseTestCase
{
    public function testValidData(): void
    {
        $firstItemEntity = new UnitAvailabilityEntity(
            22918,
            true,
            [
                new UnitAvailabilityStateEntity('2016-11-21', AvailabilityState::Available),
            ]
        );

        $secondItemEntity = new UnitAvailabilityEntity(
            22919,
            false,
            []
        );

        $response = $this->createResponse([
            'from' => '2016-11-21',
            'to' => '2016-11-30',
            'items' => [
                [
                    'ID' => 22918,
                    'isAvailable' => true,
                    'availabilityStatus' => [
                        [
                            'day' => '2016-11-21',
                            'status' => 'A',
                        ],
                    ],
                ],
                [
                    'ID' => 22919,
                    'isAvailable' => false,
                    'availabilityStatus' => [],
                ],
            ],
        ]);
        $this->assertEquals([$firstItemEntity, $secondItemEntity], $response->items());
    }

    public function testInvalidData(): void
    {
        $this->expectException(MissingValueForKeyException::class);
        $this->expectExceptionMessage('Data is missing a value for a key <from>');
        $this->createResponse([
            'test' => 'nothing',
        ]);
    }

    protected function createResponse(array $responseData): UnitsAvailabilitiesResponse
    {
        $this->expectContainerMake(new JsonToUnitAvailabilityEntity());

        return new UnitsAvailabilitiesResponse(
            new ResponseMock(),
            new GetValue(new ArrayData($responseData)),
            $this->container
        );
    }
}
```

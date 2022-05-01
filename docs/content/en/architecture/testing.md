---
title: Testing
category: Architecture
subTitle: Tools for testing.
position: 16
---

We do like unit test. We have provided basic TestCase using `Mockery` and `PHPUnit`.

## ResponseTestCase

Test case that is designed for testing responses.

Creates `SDKContainerFactoryContract` mock under `$this->container`.

### createTransformerMockViaContainer

Creates a mock of given transformer class and tells the container to return if it should be created.

### createJsonResponse

Returns mock for response interface that returns json array.

### Example

```php
class UnitsAvailabilitiesResponseTest extends ResponseTestCase
{
    private JsonToUnitAvailabilityEntity|MockInterface $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = $this->createTransformerMockViaContainer(
            JsonToUnitAvailabilityEntity::class
        );
    }
    

    public function testValidData(): void
    {
        $firstItem = [
            'ID' => 22918,
            'isAvailable' => true,
            'availabilityStatus' => [
                [
                    'day' => '2016-11-21',
                    'status' => 'A',
                ],
            ],
        ];
        $firstItemEntity = new UnitAvailabilityEntity(
            22918,
            true,
            []
        );

        $this->transformer->shouldReceive('transform')
            ->twice()
            ->with($firstItem)
            ->andReturn($firstItemEntity);

        $secondItem = [
            'ID' => 22919,
            'isAvailable' => false,
            'availabilityStatus' => [],
        ];
        $secondItemEntity = new UnitAvailabilityEntity(
            22919,
            false,
            []
        );

        $this->transformer->shouldReceive('transform')
            ->twice()
            ->with($secondItem)
            ->andReturn($secondItemEntity);

        $response = $this->createResponse([
            'from' => '2016-11-21',
            'to' => '2016-11-30',
            'items' => [
                $firstItem,
                $secondItem,
            ],
        ]);

        $foundFirstEntity = false;
        $foundSecondEntity = false;

        $this->assertEquals(true, $response->isSuccessful());
        $this->assertEquals([$firstItemEntity, $secondItemEntity], $response->items());
        $this->assertEquals(true, $response->loopItems(
            function (UnitAvailabilityEntity $entity) use (
                $firstItemEntity, &$foundFirstEntity, $secondItemEntity, &$foundSecondEntity
            ) {
                if ($entity === $firstItemEntity) {
                    $foundFirstEntity = true;
                }
                if ($entity === $secondItemEntity) {
                    $foundSecondEntity = true;
                }
            })
        );

        $this->assertTrue($foundFirstEntity, 'loopItems should return correct entity');
        $this->assertTrue($foundSecondEntity, 'loopItems should return correct entity');
    }

    public function testInvalidDataGetTo(): void
    {
        $response = $this->createResponse([
            'test' => 'nothing',
        ]);

        $this->expectErrorMessage('must not be accessed before initialization');
        $this->assertEquals(false, $response->to);
    }

    /**
     * @throws InvalidJsonResponseException
     */
    protected function createResponse(array $responseData): UnitsAvailabilitiesResponse
    {
        return new UnitsAvailabilitiesResponse(
            $this->createJsonResponse($responseData),
            $this->container
        );
    }
}
```

## EndpointTestCase

Testcase that prepares basic API mocking:

- Builds `$this->uri : Uri` from with `https://localhost/test'` value.
- Mocks `$this->apiFactory : ApiFactory`
- Mocks `$this->api : AbstractApi` and returns `$this->uri` as a base url and `$this->apiFatory` for `factory()` calls.

### expectPost

Allows to expect a `post` call on api object. Provides a closures to validate the arguments:

```php
/**
 * @param string $expectedUri expected url from the base url
 * @param Closure(mixed):bool|null $assertBody    Checks the body. Return false if not valid.
 * @param Closure(mixed):bool|null $assertHeaders Checks the headers. Return false if not valid.
 */
protected function expectPost(
    string $expectedUri,
    ?Closure $assertBody = null,
    ?Closure $assertHeaders = null,
): ExpectationInterface;
```

### expectMakeResponse

Tells the container to return given response when the functions calls `container->makeResponse`.

```php
 protected function expectMakeResponse(
    string $expectedClass,
    ExpectationInterface $expectedResponse
): MockInterface;
```

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

        $response = $this->expectPost(
            self::BASE_URI . '/unitsavailabilities',
            function (GetAvailabilityOptions $givenOptions) use ($options) {
                return $options === $givenOptions;
            }
        );

        $expectedResponse = $this->expectMakeResponse(
            UnitsAvailabilitiesResponse::class,
            $response
        );

        $endpoint = new UnitsAvailabilitiesEndpoint($this->api);
        $result = $endpoint->get($options);

        $this->assertSame($expectedResponse, $result);
    }
}
```

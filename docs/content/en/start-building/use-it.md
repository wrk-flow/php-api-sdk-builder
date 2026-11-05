---
title: How to use your API
menuTitle: Use it
description: 'You have created your API, now it is time to use it.'
category: Start building
position: 24
---

Supply your API's environment and factory through the application's composition
code. In Laravel, inject `MakeApiFactory` into the class that builds API clients:

```php
namespace ExampleSdk;

use ExampleSdk\Environments\LiveEnvironment;
use WrkFlow\ApiSdkBuilder\Actions\MakeApiFactory;

final class ExampleApiFactory
{
    public function __construct(private readonly MakeApiFactory $makeApiFactory)
    {
    }

    public function create(string $apiKey): ExampleApi
    {
        return new ExampleApi(
            environment: new LiveEnvironment($apiKey),
            factory: $this->makeApiFactory->execute(),
        );
    }
}
```

Outside Laravel, supply a compatible `SDKContainerFactoryContract` to
`MakeApiFactory`, or construct `ApiFactory` with all its required PSR services.
The factory needs request, client, stream, container, and response implementations.

Call `$api->units()->get('42')` to receive `UnitResponse`. Catch SDK exceptions at
the application's external API boundary. See [Responses](/architecture/responses)
for status handling and [Testing](/architecture/testing) for network-free checks.

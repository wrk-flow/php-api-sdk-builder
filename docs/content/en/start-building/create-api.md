---
title: Create API
category: Start building
position: 21
---

Create an environment for the API's URL and credentials. See
[Environments](/architecture/environments) for a complete `LiveEnvironment` example.

Extend `AbstractApi` and add a typed accessor for each endpoint:

```php
namespace ExampleSdk;

use ExampleSdk\Endpoints\Units\UnitsEndpoint;
use WrkFlow\ApiSdkBuilder\AbstractApi;

final class ExampleApi extends AbstractApi
{
    public function headers(): array
    {
        return [];
    }

    public function units(): UnitsEndpoint
    {
        return $this->makeEndpoint(UnitsEndpoint::class);
    }
}
```

The API implements `headers(): array` for API-wide defaults. Return an empty
array when the environment and endpoints provide all headers. Keep credentials in the environment. Supply environment-wide headers through
its `headers()` method and operation-specific headers through endpoint calls.
The API receives an environment and an `ApiFactoryContract` through its inherited
constructor. See [Create endpoint](/start-building/create-endpoint) and
[Use your API](/start-building/use-it).

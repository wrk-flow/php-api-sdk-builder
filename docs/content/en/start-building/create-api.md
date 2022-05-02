---
title: Create API
category: Start building
position: 21
---

This tutorial will guide you to create your OWN as open source. If you need private usage, just add it to your namespace
domain.

Using namespace `MaggelanoApi` in `src` directory.

## 1. Create you environment

First you want set up the base url and get some additional data from the consumer. For this we will create environment.

Create file at `src/Environments/LiveEnvironment.php` and implement uri. This is not the place to set base `headers` for
your API.

```php
namespace MaggelanoApi\Environments;

use \WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use \JustSteveKing\UriBuilder\Uri;

class LiveEnvironment  extends AbstractEnvironment  {
    public function uri(): Uri {
        return Uri::fromString('https://mgapi.mainapps.com/api/magellano');
    }
}
```

> Leave $headers if you want to allow custom headers. Can be used to pass UserAgent header.

You can override `__constructor` for passing any data you want. Environment can be accessed from endpoints using:

```php
$this->api->environment()
```

### Authorization via query

1. Override `__construct` and add new property `public readonly string $apiKey`. Make it public and readonly.
2. Use `addQueryParam` function on the uri. This will ensure that all built URIs will have the query parameter.

```php
namespace MaggelanoApi\Environments;

use \WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use \JustSteveKing\UriBuilder\Uri;

class LiveEnvironment  extends AbstractEnvironment  {
    public function __construct(public readonly string $apiKey, array $headers = []) {
        parent::__construct($headers)
    }
    
    public function uri(): Uri {
        return Uri::fromString('https://mgapi.mainapps.com/api/magellano')
            ->addQueryParam('apiKey', $this->apiKey);
    }
}
```

### Authorization via header (Bearer token)

1. Override `__construct` and add new property `public readonly string $apiKey`. Make it public and readonly.
2. Override `headers() : array` function and use `BearerTokenAuthorizationHeader` header. Do not forget to merge base
   headers.

```php
namespace MaggelanoApi\Environments;

use WrkFlow\ApiSdkBuilder\Headers\BearerTokenAuthorizationHeader;
use \WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

class LiveEnvironment extends AbstractEnvironment  {
    public function __construct(public readonly string $apiKey, array $headers = []) {
        parent::__construct($headers)
    }
    
    public function headers() : array {
        return array_merge(parent::headers(), [new BearerTokenAuthorizationHeader($this->apiKey)]);
    }
}
```

## 2. Create API class

1. Create a file located in `src/MaggelanoApi.php`
2. Extend `WrkFlow\ApiSdkBuilder\AbstractApi`

```php
namespace MagellanoApi;

use App\ChannelManager\Magellano\Api\Endpoints\Units\UnitsEndpoint;
use App\ChannelManager\Magellano\Api\Endpoints\UnitsAvailabilities\UnitsAvailabilitiesEndpoint;
use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;

class MagellanoApi extends AbstractApi
{
    
}
```

### Implement headers

For this example we want to tell API that we are sending JSON, and we want to receive JSON data.

If you do not want any headers set then return empty array.

```php
public function headers(): array
{
    return [new JsonHeaders()];
}
```

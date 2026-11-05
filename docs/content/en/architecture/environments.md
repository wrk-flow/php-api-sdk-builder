---
title: Environments
category: Architecture
fullscreen: true
position: 12
---

An environment supplies the base URI and headers for an API instance. Keep
credentials and other caller-supplied configuration here. Extend
`AbstractEnvironment` and implement `uri(): UriInterface`.

```php
namespace ExampleSdk\Environments;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Headers\BearerTokenAuthorizationHeader;

final class LiveEnvironment extends AbstractEnvironment
{
    public function __construct(public readonly string $apiKey, array $headers = [])
    {
        parent::__construct($headers);
    }

    public function uri(): UriInterface
    {
        return new Uri('https://api.example.com/v1');
    }

    public function headers(): array
    {
        return [...parent::headers(), new BearerTokenAuthorizationHeader($this->apiKey)];
    }
}
```

The parent constructor accepts headers. `addHeader()` mutates that header list;
do not make the whole environment readonly when callers use this method.
An endpoint can read its environment through `$this->di->api()->environment()`.
For query-based authentication, build the query with `http_build_query()` and
PSR URI `withQuery()`. Preserve any existing query parameters.

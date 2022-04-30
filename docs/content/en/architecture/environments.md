---
title: Environments
category: Architecture
fullscreen: true
position: 12
---

> Place your environments in `YourApi\Environments` namespace and use Environment suffix.

Environment is used for defining base URL and base headers for the given environment (like passing `Bearer` token).
Environments are a good place to get "consumer" data to pass data to the API (credentials, headers, etc).

If you are planning to have multiple environments I would recommend creating `AbstractYourApiEnvironment` and then
creating something like `LiveEnvironment`.

## Example

Here as an example with passing API key using query parameter 🤔

```php
namespace MagellanoApi\Environments;

use JustSteveKing\UriBuilder\Uri;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

class LiveEnvironment extends AbstractEnvironment
{
    public function __construct(
        public readonly string $apiKey,
        array $headers = [],
    ) {
        parent::__construct($headers);
    }

    public function uri(): Uri
    {
        return Uri::fromString('https://mgapi.mainapps.com/api/magellano')
            ->addQueryParam('apiKey', $this->apiKey);
    }
}
```

## Headers

Headers can be provided with the environment and can be built dynamically within the environment by extending `headers`:

```php
namespace MagellanoApi\Environments;

use JustSteveKing\UriBuilder\Uri;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

class LiveEnvironment extends AbstractEnvironment
{
    public function __construct(
        public readonly string $apiKey,
        array $headers = [],
    ) {
        parent::__construct($headers);
    }

    public function uri(): Uri
    {
        return Uri::fromString('https://mgapi.mainapps.com/api/magellano');
    }

    public function headers(): array
    {
        return array_merge(parent::headers(), [new MyCustomHeader($this->apiKey)], 'X-KEY' => $this->apiKey);
    }
}
```

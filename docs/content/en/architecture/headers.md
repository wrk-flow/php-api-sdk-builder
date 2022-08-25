---
title: Headers
category: Architecture
position: 15
fullscreen: true
---

> Use `YourApi\Headers` namespace

For building passing / using headers you can use pre-build (or create your own) classes that will allow stacking
headers into reusable classes.

When ever you want to pass headers in environment or return it in endpoint you are advised to use header classes but you
can return a map of headers 'key' => 'header' or 'key' => 'headers'.

**Available classes (fell free to PR more):**

- `new JsonContentTypeHeaders()`
- `new AcceptsJsonHeaders()`
- `new JsonHeaders()` (sets the content type and accepts json headers)
- `BearerTokenAuthorizationHeader(string $token)`

Each header class can chain other headers (check JsonHeaders file).

<code-group>
  <code-block label="Simple header" active>

  ```php
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class AcceptsJsonHeaders implements HeadersContract
{
    public function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }
}
  ```

  </code-block>
  <code-block label="Combining headers">

  ```php
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class JsonHeaders implements HeadersContract
{
    public function headers(): array
    {
        return [
            new JsonContentTypeHeaders(), 
            new AcceptsJsonHeaders(),
            'X-test' => 'test',
        ];
    }
}
  ```

  </code-block>
</code-group>


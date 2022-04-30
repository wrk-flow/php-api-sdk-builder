---
title: How to use your API
menuTitle: Use it
description: 'You have created your API, now it is time to use it.'
category: Start building
position: 24
---

TODO


- [Nyholm/psr7](https://github.com/Nyholm/psr7)

```php
// Better - use dependency injections in your methods
$container = app(LaravelContainerFactory::class);
$api = new MyApi(
    new LiveEnvironment($apiKey),
    new ApiFactory(
        new RequestFactory(),
        new Client(),
        new StreamFactory(),
        $container
    )
);

// Your own response object 
$response = $api->namespaces()->paginate();
```

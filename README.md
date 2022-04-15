### A base package for building unified API SDKs with type strict and dependency in mind

> Work in progress

```bash
composer require wrkflow/php-api-sdk-builder
```

## How to use API SDK

Every API should have only 2 parameters: `environment` and `ApiFactory`.

- Environment defines base url, headers. Environment should accept credentials.
- ApiFactory provides request factory, stream factory, client factory and container to allow dependency injection.
    - You can use already used PSR-7 HTTP library in your code (or use [Nyholm/psr7](https://github.com/Nyholm/psr7))
    - You can use already pre-pared container wrappers
        - Laravel: `new \WrkFlow\ApiSdkBuilder\Containers\LaravelContainerFactory()`
        - Or provide your own container by implementing SDKContainerFactoryContract

You can use prepared

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

## How to build API SDK

### Architecture

Each API consists:

- environments
- endpoints (and input options)
- headers
- responses (you are advised to use them, but this library does not force them).

All endpoints and responses allows dependency injection. **Ensure that parent __construct arguments are not renamed**. 

#### Environments

> Use `YourApi\Environments` namespace

If you are planning to have multiple environments I would recommend creating `AbstractYourApiEnvironment` and then
creating something like `LiveEnvironment`.

Environment are a good place to get "consumer" data to setup the API (credentials, headers, etc).

#### Headers

> Use `YourApi\Headers` namespace

For building passing / using headers you can use pre-build (or create your own) classes that will allow stacking
headers into reusable classes.

When ever you want to pass headers in environment or return it in endpoint you are advised to use header classes but you
can return a map of headers 'key' => 'header' or 'key' => 'headers'.

**Available classes (fell free to PR more):**

- JsonContentTypeHeaders
- AcceptsJsonHeaders
- JsonHeaders (sets the content type and accepts json headers)

Each header class can chain other headers (check JsonHeaders file).

#### Endpoints

TODO

#### Naming conventions

TODO


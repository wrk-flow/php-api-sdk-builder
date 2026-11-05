---
title: Frameworks
category: Architecture
subTitle: These frameworks are implemented and ready to use.
position: 16
---

## Laravel

Composer package discovery registers
`WrkFlow\ApiSdkBuilder\Laravel\LaravelServiceProvider`. The provider binds
`SDKContainerFactoryContract` to `LaravelContainerFactory`, and registers
request handling, logging, configuration, and console services.

Construct your API with an environment and an API factory. Inject
`MakeApiFactory` in a supported application constructor or lifecycle method,
then call `execute()` to build the factory. See [Use your API](/start-building/use-it).

The SDK works without LaraStrict. When LaraStrict conventions are installed,
PHPStan can load the optional SDK placement and injection adapters.

## Other containers

Implement `SDKContainerFactoryContract` for another container. Its methods
resolve collaborators, construct endpoints with their API dependencies, and
construct response classes with the HTTP response and parsed body. Inspect the
installed interface and test these construction paths before using the adapter.

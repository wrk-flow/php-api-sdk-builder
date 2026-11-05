---
title: Architecture and conventions
menuTitle: Architecture
subtitle: 'To properly create your API it is important to understand the conventions and the architecture 🚀'
category: Architecture
position: 11
---

The SDK separates HTTP operations, caller inputs, and parsed results:

- **API:** extends `AbstractApi` and exposes typed endpoint accessors.
- **Environment:** defines the base URI, credentials, and headers.
- **Endpoint:** groups HTTP operations and returns concrete response types.
- **Options:** serializes caller-supplied data for the external API.
- **Response:** parses the HTTP result into typed fields or Entities.
- **Factory:** supplies PSR HTTP services and the SDK container.

## API and factory

Use `makeEndpoint(UnitsEndpoint::class)` in an API accessor. The SDK factory
constructs endpoints with their runtime dependencies. Preserve the inherited
API constructor's environment and factory arguments. See
[Create API](/start-building/create-api) and [Use your API](/start-building/use-it).

`ApiFactory` accepts request, client, stream, container, and response services,
plus optional event dispatch and logging configuration. `MakeApiFactory` can
find compatible PSR implementations through HTTP discovery.

## Entities and transformers

Keep endpoint-specific DTOs beside their endpoint. Put shared DTOs in `Entities`.
Use typed fields and immutable result Entities where appropriate. Options and
Environments remain caller inputs with their own lifecycle requirements.

For item responses, implement
`Wrkflow\GetValue\Contracts\GetValueTransformerContract` with
`transform(GetValue $value, string $key)`. Return the concrete Entity type and
validate required fields through `GetValue`. See [Responses](/architecture/responses).

## Development and AI conventions

StrictPHP supplies the package's Rector, ECS, and PHPStan configuration. The
`api-sdk-builder` skill adds SDK construction, serialization, and testing guidance.
Optional LaraStrict adapters allow endpoint-local Entities and check factory
argument names in Laravel applications. See the package README for registration
and development commands.

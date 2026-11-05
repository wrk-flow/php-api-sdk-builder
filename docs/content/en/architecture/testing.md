---
title: Testing
category: Architecture
subTitle: Tools for testing.
position: 16
---

Test serialization, request construction, and response parsing separately from
network transport.

## Endpoint requests

Extend `Testing\Endpoints\EndpointTestCase` and use `assertEndpoint()`.
Pass a closure that receives `EndpointDIEntity` and calls your endpoint. Supply
the expected URI suffix, method, body, headers, and expected status code.
The helper constructs test dependencies and checks the request without sending
it to the network.

For more control, use `EndpointDIEntityFactory` and
`SendTestRequestActionAssert`. These helpers exercise the same runtime argument
boundaries as an SDK endpoint.

## Responses

Construct a concrete response with `JsonResponseMock` and a `GetValue` created
from `DataHolders\ArrayData`. Assert parsed fields, nested Entities, pagination,
and behavior for invalid required fields.

`Testing\Responses\ResponseTestCase` provides a mocked SDK container and
`expectContainerMake()` for response transformers. Item responses also need the
`$container` constructor argument.

## Options and integration

Use `Testing\Options\OptionsTestCase` when it fits your test runner. Verify
wire keys, nullable fields, and serialization with the environment used by the
operation.

Use mock HTTP clients for transport integration tests. Test Laravel container
bindings separately from response parsing. Normal package QA runs with
`composer qa`; optional LaraStrict adapter tests use `composer test:larastrict`
with compatible conventions available.

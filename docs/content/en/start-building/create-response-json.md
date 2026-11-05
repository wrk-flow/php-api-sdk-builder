---
title: Create response from JSON
category: Start building
position: 23
---

Create `Endpoints/Units/UnitResponse.php` beside the endpoint. Extend
`AbstractJsonResponse` and keep constructor arguments named `$response` and
`$body`, typed as PSR `ResponseInterface` and `GetValue`.

Call the parent constructor, then use required getters to initialize typed
response fields. The [response guide](/architecture/responses#json-response)
contains a complete `UnitResponse` example.

Return `UnitResponse` from your endpoint operation and pass
`responseClass: UnitResponse::class` to the request helper. The SDK parses JSON
and constructs the response. Add tests for valid fields and invalid input.

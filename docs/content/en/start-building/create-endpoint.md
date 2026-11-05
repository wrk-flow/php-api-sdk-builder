---
title: Create endpoint
category: Start building
position: 22
---

Create `Endpoints/Units/UnitsEndpoint.php`, extend `AbstractEndpoint`, and
implement `basePath()`. Add a public operation returning a concrete response.

Use `sendGet()` for a GET request and `sendPost()`, `sendPut()`, or
`sendDelete()` for other operations. Pass `responseClass` and `uri` explicitly.
Use Options to serialize request bodies.

The [endpoint guide](/architecture/endpoints#implementation) contains a complete
`UnitsEndpoint` example. Then implement its
[JSON response](/start-building/create-response-json).

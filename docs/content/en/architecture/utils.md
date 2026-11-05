---
title: Utils
category: Architecture
subTitle: Tools for testing.
position: 17
---

## Typed response data

JSON and XML responses expose a `Wrkflow\GetValue\GetValue` through `body()`.
Use its typed getters to read data, such as `getRequiredString()`,
`getRequiredInt()`, and `getRequiredBool()`. Use nullable getters when the API
allows a missing value.

For nested records, use `getRequiredArrayGetter()` and map the record to a typed
Entity. Item responses can apply a `GetValueTransformerContract` to each record.
Inspect the installed `wrkflow/php-get-typed-value` version for available getters
and transformer signatures. See [Responses](/architecture/responses).

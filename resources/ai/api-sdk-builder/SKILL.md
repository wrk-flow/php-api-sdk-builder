---
name: api-sdk-builder
description: Apply ApiSdkBuilder conventions when creating, changing, or reviewing SDK endpoints, Options, Responses, environments, and API factories using wrkflow/php-api-sdk-builder.
---

# ApiSdkBuilder conventions

Read the shared [StrictPHP skill](../strictphp/SKILL.md) first. If the application uses LaraStrict, also read the `larastrict` skill and its relevant reference. Read the application's applicable instructions and the installed SDK source before changing a signature. Source paths below resolve from the application root under `vendor/wrkflow/php-api-sdk-builder/`.

- Group an endpoint and its Options, Responses, and Entity DTOs in `Endpoints/{endpoint}`. Nested endpoint folders are allowed. Shared DTOs may use `Entities`.
- Endpoint classes may expose several named operations. Their operation arguments are caller inputs, not method injection points.
- `src/Laravel/LaravelContainerFactory.php` constructs endpoints with `$di` and responses with named `$response`, `$body`, and `$container` overrides. Preserve those names when a constructor accepts the corresponding runtime arguments. In SDK response constructors, name `GetValue` input `$body`. This convention does not apply to unrelated GetValue helpers or transformation methods.
- Endpoint and response constructors may also receive injected collaborators. Use normal collaborator names for those additional dependencies. Keep the SDK runtime arguments distinct from those dependencies. Do not test endpoint or response resolvability with a plain container call that omits the SDK arguments.
- Options and Environments carry caller-supplied input and configuration. Construct them with their actual values. Do not convert their constructors into service injection points or make them readonly without checking their lifecycle.
- Options `toArray()` and response parsing are protocol boundaries. Serialized arrays, external field names, and SDK response accessors belong here. Do not apply application HTTP Resource conventions to an external API's wire format.
- Keep tenant and role selection in the application's API factory. A factory that owns scoped bindings may need an explicit Larastrict container-composition registration. Do not exempt all factories.

Use `src/Endpoints/AbstractEndpoint.php`, `src/Responses/AbstractJsonResponse.php`, and `src/Options/AbstractJsonOptions.php` for the supported signatures. Reuse `src/Testing/` helpers when they fit the consumer's test setup. Test serialization and response parsing separately from network requests.

When enabling rules, use PHPStan extension discovery or the conditional `phpstan/extension.php` loader. The SDK adapter skips LaraStrict integration when its interface is unavailable. The `strictphp-sync-ai` command installs this skill from the package's Composer registration; edit its package source, not the generated application copy.

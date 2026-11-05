# API SDK builder conventions

## MUST USE

- Use `$api-sdk-builder` for SDK implementation details.
- Preserve SDK factory arguments and external wire formats.

## DON'T USE

- Do not apply application Resource conventions to external API payloads.
- Do not treat caller-supplied SDK inputs as injected services.

# Changelog

## Unreleased

### 🛠️ Development tooling

- Use `strictphp/conventions:^2.1` for Rector, ECS, and PHPStan instead of separate tool pins.
- Require PHP 8.3 or later for development tools. Keep the runtime PHP requirement at 8.1 or later.
- Update the Laravel adapter test stack to Laravel 10, Testbench 8, and PHPUnit 10.
- Run Rector, then ECS, then PHPStan, with tests last. Keep `composer check` as an alias for `composer qa`.
- Apply shared file headers and formatting, update data providers, and fix issues reported by PHPStan 2.

### 🤖 Package AI integration

- Register the general `$api-sdk-builder` skill and short package rules through `extra["strictphp/conventions"]`.
- Keep Larastrict placement and injection integration optional. Normal SDK development uses StrictPHP.

### 📦 Release archives

- Exclude vendor dependencies, local lock files, test caches, and development files from Composer archives.

### 🐛 Configuration validation

- Reject invalid logger maps and logger classes with an explicit configuration error.

[Changelog at php-sdk-builder.wrk-flow.com/releases](https://php-sdk-builder.wrk-flow.com/releases)

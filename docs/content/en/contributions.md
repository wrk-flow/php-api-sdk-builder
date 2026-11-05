---
title: Contributions
subtitle: 'Contribute headers, abstractions, frameworks.'
position: 2
---

Feel free to contribute. When contributing try to match this category:

## Bug fixes

For bug fixes create and issue and pull request if possible.

## Ideas

Use the discussion functionality and propose your idea:

- What you want to solve?
- Sample proof of concept code? (just how it could look)

## Wait to take in account

- Always design your classes with dependency injection in mind (possibly constructor).
- Always think about tests -> how they should be written and if it is easy.

## Checklist

- [ ] Run `composer run check`.
- [ ] Write PHPUnit tests.
- [ ] Update the documentation.

## Lint and tests

Development requires PHP 8.3 or later. StrictPHP Conventions supplies Rector,
ECS, and PHPStan. Install dependencies with `composer install`.

Run `composer qa` to apply Rector and ECS, then run PHPStan and tests.
For checks without code transformations, use:

```bash
composer lint:upgrade:check
composer lint:check
composer analyse
composer test
```

The runtime SDK supports PHP 8.1 or later. Optional Larastrict adapter tests run
with `composer test:larastrict` when compatible Larastrict conventions are
available.

## Contributors

Check our contributors on [GitHub](https://github.com/wrk-flow/php-api-sdk-builder/graphs/contributors)

## Updating documentation

We are using [Nuxtjs](https://content.nuxtjs.org) and every new addition should be documented. It's easy:

1. Go to **docs** folder

    ```bash
    npm install
    ```
2. Start the dev server and open [localhost:3000](http://localhost:3000)

    ```bash
    npm run dev
    ```
3. Docs content is located at **content/en/** and written in Markdown.
4. When creating new pages you need to set `position`. To fix navigation and correct position per folder we are
   using `position + X0` where X is the position of the folder.


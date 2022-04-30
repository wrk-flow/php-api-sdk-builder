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

```bash
composer run check
```

We are using set of tools to ensure that the code is consistent. Run this before pushing your code changes.

### [PHPStan](https://phpstan.org)

```bash
composer run check
```

### [Rector](https://github.com/rectorphp/rector)

```bash
composer run check
```

### [Easy coding standard](https://github.com/symplify/easy-coding-standard)

```bash
composer run check
```

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


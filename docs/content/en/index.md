---
title: Introduction
subtitle: 'This package helps you build PHP clients for external APIs with dependency injection and type strict code in mind.'
position: 1
---

<img src="https://img.shields.io/badge/PHPStan-9-blue" class="inline-flex" style="margin: 0;" />
<img src="https://img.shields.io/badge/PHP-8.1-B0B3D6"  class="inline-flex" style="margin: 0;" />
<img src="https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/pionl/bdae700e21749c3532d7961f1d8b252b/raw/coverage.json"  class="inline-flex" style="margin: 0;" />

## Why to use?

- If you want to consume external API without losing type strict code (Data transfer objects instead of array keys).
- If you work with multiple APIs and want to use the same conventions.
- If you want to provide type strict way to build request parameters.
- If you want to save time building HTTP/s request.

## Features

- 🛠 Dependency injection using your favorite framework (Laravel, _PR for more_)
- ✅ Uses PSR packages you already use for HTTP/S communication
- 🏆 Forcing type strict implementation for input (endpoint options) and output (**Response**)
- 🎗 Encouraging **Data transfer objects**
- 🎭 Re-usable and configurable headers using objects

## Installation

```bash
composer require wrkflow/php-api-sdk-builder
```

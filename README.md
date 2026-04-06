# Emizor PHP SDK

[![Latest Stable Version](https://poser.pugx.org/10quality/emizor-php/v/stable)](https://packagist.org/packages/10quality/emizor-php)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/10quality/emizor-php/ci.yml)
[![Total Downloads](https://poser.pugx.org/10quality/emizor-php/downloads)](https://packagist.org/packages/10quality/emizor-php)
[![License](https://poser.pugx.org/10quality/emizor-php/license)](https://packagist.org/packages/10quality/emizor-php)

PHP SDK for the Emizor 5 REST API with Guzzle transport, `.env` support, PHPUnit coverage, and static analysis.

## Requirements

* PHP `^8.2`
* Composer `^2`

## Features

* Injectable SDK client under the `Emizor` namespace.
* Guzzle-based HTTP transport with Emizor authentication headers.
* Environment-backed configuration via `EMIZOR_*` variables.
* Coverage for all endpoints documented in the bundled Emizor 5 REST API reference.
* Enum support for documented API constants such as document and payment types.
* PHPUnit and PHPStan integration for testing and code quality.

## Installation

```bash
composer install
```

Or install the package in another project:

```bash
composer require 10quality/emizor-php
```

## Environment Variables

```dotenv
EMIZOR_BASE_URL=https://{your-env}.emizor.com
EMIZOR_API_TOKEN=your-api-token
EMIZOR_API_SECRET=password
```

## Quick Start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Emizor\Emizor;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$emizor = Emizor::fromEnvironment();
$products = $emizor->products()->list();

print_r($products);
```

## Available Endpoints

* Verify NIT: `verifyNit(string $nit)`
* Create client: `clients()->create(array $payload)`
* Create invoice or prefactura: `invoices()->create(array $payload, ?InvoiceOptions $options = null)`
* Generate QR for prefactura: `invoices()->generateQr(array $invoiceIds, bool $publish = false)`
* Emit prefactura: `invoices()->emitPrefactura(string $originId)`
* Check invoice status: `invoices()->status(string $originId)`
* List products: `products()->list(int $page = 1, string $filter = '')`

## Docs

* Main usage guide: [docs/README.md](docs/README.md)
* Endpoint guide: [docs/endpoints.md](docs/endpoints.md)
* Full workflow sample: [docs/samples/all-endpoints.php](docs/samples/all-endpoints.php)
* Basic sample: [docs/samples/basic-usage.php](docs/samples/basic-usage.php)

## Quality Checks

```bash
composer analyse
composer test
composer test:coverage
```

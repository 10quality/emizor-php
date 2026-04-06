# Emizor PHP SDK

The Emizor PHP SDK provides a focused, injectable client for the Emizor 5 REST API documented in the official API reference.

## Features

* Guzzle-based HTTP transport.
* Environment-aware configuration via `.env`.
* Dedicated resources for clients, invoices, products, and tax verification flows.
* Enum support for documented constant values.
* PHPUnit test suite with full source coverage.

## Installation

```bash
composer require emizor/emizor-php-sdk
```

If you are developing this package locally, install dependencies with:

```bash
composer install
```

## Configuration

Create a `.env` file in your project root:

```dotenv
EMIZOR_BASE_URL=https://felapp2.emizor.com
EMIZOR_API_TOKEN=your-api-token
EMIZOR_API_SECRET=password
```

Then load the variables before building the SDK:

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Emizor\Emizor;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$emizor = Emizor::fromEnvironment();
```

## Authentication

The SDK sends the headers required by the official Emizor API documentation:

* `X-Api-Token`
* `X-Api-Secret`
* `X-Requested-With: XMLHttpRequest`
* `Accept: application/json`
* `Content-Type: application/json`

## Available Resources

* `verifyNit(string $nit)`
* `clients()->create(array $payload)`
* `invoices()->create(array $payload, InvoiceOptions $options)`
* `invoices()->generateQr(array $invoiceIds, bool $publish = false)`
* `invoices()->emitPrefactura(string $originId)`
* `invoices()->status(string $originId)`
* `products()->list(int $page = 1, string $filter = '')`

## Documented Enums

The SDK includes enum classes for values explicitly documented by Emizor:

* `Emizor\Enums\DocumentType`
* `Emizor\Enums\PaymentMethod`
* `Emizor\Enums\CurrencyCode`
* `Emizor\Enums\SectorDocumentType`

## Usage Samples

See the sample scripts:

* [docs/samples/basic-usage.php](samples/basic-usage.php)

## Testing

Run the test suite:

```bash
composer test
```

Run coverage:

```bash
composer test:coverage
```

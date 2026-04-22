# Endpoint Guide

This guide documents each endpoint implemented by the SDK based on the bundled Emizor 5 REST API reference.

## Bootstrapping the SDK

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Emizor\Emizor;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$emizor = Emizor::fromEnvironment();
```

## 1. Verify NIT

Endpoint: `GET /api/v1/clientfel/verify_nit/{nit}`

```php
$result = $emizor->verifyNit('9861234011');
```

The API returns a `success` flag that can be used to derive `codigoExcepcion` for invoice payloads.

## 2. Create Client

Endpoint: `POST /api/v1/clients`

Supported `felData.type_document_id` values are available through `DocumentType`:

| Emizor code | Enum case | Description |
|-------------|-----------|-------------|
| `1` | `DocumentType::CI` | Cedula de identidad |
| `2` | `DocumentType::CEX` | Cedula de identidad de extranjero |
| `3` | `DocumentType::PAS` | Pasaporte |
| `4` | `DocumentType::OD` | Otro documento de identidad |
| `5` | `DocumentType::NIT` | Numero de identificacion tributaria |

```php
use Emizor\Enums\DocumentType;

$client = $emizor->clients()->create([
    'name' => 'Demo Customer',
    'phone' => '70000000',
    'number' => 'CUST-001',
    'felData' => [
        'type_document_id' => DocumentType::NIT->value,
        'business_name' => 'Demo Customer SRL',
        'document_number' => '9861234011',
        'complement' => null,
    ],
    'contacts' => [
        [
            'first_name' => 'Demo',
            'last_name' => 'Customer',
            'email' => 'demo@example.com',
            'phone' => '70000000',
            'send_email' => true,
        ],
    ],
]);
```

## 3. Create Invoice

Endpoint: `POST /api/v1/invoices?should_emit=true&paid=false&send_mail=true`

```php
use Emizor\Dto\InvoiceOptions;
use Emizor\Enums\CurrencyCode;
use Emizor\Enums\DocumentType;
use Emizor\Enums\PaymentMethod;
use Emizor\Enums\SectorDocumentType;

$invoice = $emizor->invoices()->create([
    'client_id' => $client['data']['id'],
    'line_items' => [
        [
            'quantity' => 1,
            'cost' => 100,
            'product_key' => 'SERVICE',
            'product_id' => 'replace-with-product-id',
            'notes' => 'Invoice generated from the SDK',
        ],
    ],
    'entity_type' => 'invoice',
    'felData' => [
        'codigoMetodoPago' => PaymentMethod::CASH->value,
        'codigoMoneda' => CurrencyCode::BOB->value,
        'sector_document_type_id' => SectorDocumentType::STANDARD_INVOICE->value,
        'tipoCambio' => 1,
        'facturaTicket' => 'ticket-001',
        'codigo_sucursal' => '0',
        'descuentoAdicional' => 0,
        'montoTotal' => 100,
        'montoTotalSujetoIva' => 100,
        'codigoTipoDocumentoIdentidad' => DocumentType::NIT->value,
        'numeroDocumento' => '9861234011',
        'nombreRazonSocial' => 'Demo Customer SRL',
        'codigoExcepcion' => 0,
    ],
], new InvoiceOptions(true, false, true));
```

`PaymentMethod` now exposes the expanded Emizor catalog. For example, code `86` remains available as `PaymentMethod::MIXED`.

## 4. Create Prefactura

The same endpoint is used with `should_emit=false`.

```php
$prefactura = $emizor->invoices()->create($payload, new InvoiceOptions(false, false, true));
```

## 5. Generate QR for Prefactura

Endpoint: `POST /api/v1/invoices/bulk`

```php
$qr = $emizor->invoices()->generateQr([
    $prefactura['data']['id'],
]);
```

## 6. Emit Prefactura

Endpoint: `POST /api/v1/clientfel/invoices`

```php
$emitted = $emizor->invoices()->emitPrefactura('origin-invoice-id');
```

## 7. Check Invoice Status

Endpoint: `POST /api/v1/clientfel/invoices/status`

```php
$status = $emizor->invoices()->status('origin-invoice-id');
```

## 8. List Products

Endpoint: `GET /api/v1/products?page=1&filter=`

```php
$products = $emizor->products()->list(1, '');
```

## 9. Create Product

Endpoint: `POST /api/v1/products`

```php
$product = $emizor->products()->create([
    'product_key' => 'Minerva',
    'notes' => 'Taladros',
    'price' => '440',
    'felData' => [
        'codigo_unidad' => '57',
        'nombre_unidad' => 'UNIDAD (BIENES)',
        'codigo_actividad_economica' => '620901',
        'codigo_producto_sin' => '83151',
        'codigo' => 'Fa-052347',
        'codigo_producto' => 'Fa-052347',
    ],
]);
```

## 10. Update Product

Endpoint: `PUT /api/v1/products/{id}`

```php
$updatedProduct = $emizor->products()->update('replace-with-product-id', [
    'product_key' => 'Minerva Plus',
    'notes' => 'Taladros actualizados',
    'price' => '450',
    'felData' => [
        'codigo_unidad' => '57',
        'nombre_unidad' => 'UNIDAD (BIENES)',
        'codigo_actividad_economica' => '620901',
        'codigo_producto_sin' => '83151',
        'codigo' => 'Fa-052347',
        'codigo_producto' => 'Fa-052347',
    ],
]);
```

## 11. Delete Product

Endpoint: `DELETE /api/v1/products/{id}`

```php
$deletedProduct = $emizor->products()->delete('replace-with-product-id');
```

## 12. List Parametricas

Endpoint: `GET /api/v1/clientfel/parametricas/{parametrica}`

Supported examples from the Emizor documentation:

* `actividades`
* `productos-sin`
* `unidades`
* `metodos-de-pago`
* `tipos-documento-de-identidad`

```php
$identityDocumentTypes = $emizor->parametricas()->list('tipos-documento-de-identidad');
```

## Samples

* [docs/samples/basic-usage.php](samples/basic-usage.php)
* [docs/samples/all-endpoints.php](samples/all-endpoints.php)

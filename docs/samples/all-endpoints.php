<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Emizor\Dto\InvoiceOptions;
use Emizor\Emizor;
use Emizor\Enums\CurrencyCode;
use Emizor\Enums\DocumentType;
use Emizor\Enums\PaymentMethod;
use Emizor\Enums\SectorDocumentType;

/**
 * This sample shows one end-to-end Emizor SDK workflow and touches every
 * endpoint described in the bundled Emizor API documentation.
 */
$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

$emizor = Emizor::fromEnvironment();

$nitVerification = $emizor->verifyNit('9861234011');

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

$products = $emizor->products()->list(1, '');
$productId = $products['data'][0]['id'] ?? 'replace-with-real-product-id';
$productKey = $products['data'][0]['product_key'] ?? 'SERVICE';

$createdProduct = $emizor->products()->create([
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

$updatedProduct = $emizor->products()->update($productId, [
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

$deletedProduct = $emizor->products()->delete($productId);
$identityDocumentTypes = $emizor->parametricas()->list('tipos-documento-de-identidad');

$invoicePayload = [
    'client_id' => $client['data']['id'] ?? 'replace-with-real-client-id',
    'line_items' => [
        [
            'quantity' => 1,
            'cost' => 100,
            'product_key' => $productKey,
            'product_id' => $productId,
            'notes' => 'Invoice created from all-endpoints sample',
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
        'codigoExcepcion' => ($nitVerification['success'] ?? false) ? 0 : 1,
    ],
];

$invoice = $emizor->invoices()->create($invoicePayload, new InvoiceOptions(true, false, true));
$prefactura = $emizor->invoices()->create($invoicePayload, new InvoiceOptions(false, false, true));
$prefacturaId = $prefactura['data']['id'] ?? 'replace-with-prefactura-id';

$qr = $emizor->invoices()->generateQr([$prefacturaId]);
$emitResult = $emizor->invoices()->emitPrefactura($prefacturaId);
$status = $emizor->invoices()->status($prefacturaId);

print_r([
    'verify_nit' => $nitVerification,
    'client' => $client,
    'products' => $products,
    'created_product' => $createdProduct,
    'updated_product' => $updatedProduct,
    'deleted_product' => $deletedProduct,
    'parametricas_identity_document_types' => $identityDocumentTypes,
    'invoice' => $invoice,
    'prefactura' => $prefactura,
    'qr' => $qr,
    'emit_prefactura' => $emitResult,
    'status' => $status,
]);

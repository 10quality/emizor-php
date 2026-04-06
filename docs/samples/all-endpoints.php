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
    'invoice' => $invoice,
    'prefactura' => $prefactura,
    'qr' => $qr,
    'emit_prefactura' => $emitResult,
    'status' => $status,
]);

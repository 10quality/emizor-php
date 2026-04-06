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
 * This sample demonstrates the recommended setup for the Emizor SDK.
 * It loads environment variables, creates the injectable SDK client,
 * verifies a NIT, creates a customer, lists products, and creates an invoice.
 */
$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

$emizor = Emizor::fromEnvironment();

$nitResult = $emizor->verifyNit('9861234011');

$customer = $emizor->clients()->create([
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

$products = $emizor->products()->list();

$invoice = $emizor->invoices()->create([
    'client_id' => $customer['data']['id'] ?? 'replace-with-real-client-id',
    'line_items' => [
        [
            'quantity' => 1,
            'cost' => 100,
            'product_key' => $products['data'][0]['product_key'] ?? 'SERVICE',
            'product_id' => $products['data'][0]['id'] ?? 'replace-with-real-product-id',
            'notes' => 'Emizor SDK sample invoice',
        ],
    ],
    'entity_type' => 'invoice',
    'felData' => [
        'codigoMetodoPago' => PaymentMethod::CASH->value,
        'codigoMoneda' => CurrencyCode::BOB->value,
        'sector_document_type_id' => SectorDocumentType::STANDARD_INVOICE->value,
        'tipoCambio' => 1,
        'facturaTicket' => 'sample-ticket-001',
        'codigo_sucursal' => '0',
        'descuentoAdicional' => 0,
        'montoTotal' => 100,
        'montoTotalSujetoIva' => 100,
        'codigoTipoDocumentoIdentidad' => DocumentType::NIT->value,
        'numeroDocumento' => '9861234011',
        'nombreRazonSocial' => 'Demo Customer SRL',
        'codigoExcepcion' => ($nitResult['success'] ?? false) ? 0 : 1,
    ],
], new InvoiceOptions(true, false, true));

print_r($invoice);

<?php

declare(strict_types=1);

namespace Emizor\Tests\Resources;

use Emizor\Dto\InvoiceOptions;
use Emizor\Resources\InvoicesResource;
use Emizor\Tests\Support\FakeTransport;
use PHPUnit\Framework\TestCase;

final class InvoicesResourceTest extends TestCase
{
    public function test_it_creates_invoices_with_default_options(): void
    {
        $transport = new FakeTransport(['data' => ['id' => 'invoice-1']]);
        $resource = new InvoicesResource($transport);

        $response = $resource->create(['client_id' => 'abc']);

        self::assertSame(['data' => ['id' => 'invoice-1']], $response);
        self::assertSame([
            'should_emit' => 'true',
            'paid' => 'false',
            'send_mail' => 'true',
        ], $transport->requests[0]['query']);
    }

    public function test_it_creates_invoices_with_custom_options(): void
    {
        $transport = new FakeTransport();
        $resource = new InvoicesResource($transport);

        $resource->create(['client_id' => 'abc'], new InvoiceOptions(false, true, false));

        self::assertSame([
            'should_emit' => 'false',
            'paid' => 'true',
            'send_mail' => 'false',
        ], $transport->requests[0]['query']);
    }

    public function test_it_generates_qr_codes(): void
    {
        $transport = new FakeTransport();
        $resource = new InvoicesResource($transport);

        $resource->generateQr(['pref-1'], true);

        self::assertSame('/api/v1/invoices/bulk', $transport->requests[0]['uri']);
        self::assertSame([
            'ids' => ['pref-1'],
            'action' => 'bulk_generate_qr',
            'publish' => true,
        ], $transport->requests[0]['payload']);
    }

    public function test_it_emits_prefacturas(): void
    {
        $transport = new FakeTransport();
        $resource = new InvoicesResource($transport);

        $resource->emitPrefactura('origin-1');

        self::assertSame('/api/v1/clientfel/invoices', $transport->requests[0]['uri']);
        self::assertSame(['id_origin' => 'origin-1'], $transport->requests[0]['payload']);
    }

    public function test_it_fetches_invoice_status(): void
    {
        $transport = new FakeTransport();
        $resource = new InvoicesResource($transport);

        $resource->status('origin-2');

        self::assertSame('/api/v1/clientfel/invoices/status', $transport->requests[0]['uri']);
        self::assertSame(['id_origin' => 'origin-2'], $transport->requests[0]['payload']);
    }
}

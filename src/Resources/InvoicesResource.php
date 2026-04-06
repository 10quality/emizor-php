<?php

declare(strict_types=1);

namespace Emizor\Resources;

use Emizor\Contracts\TransportInterface;
use Emizor\Dto\InvoiceOptions;

/**
 * Handles invoice and prefactura API operations.
 */
final readonly class InvoicesResource
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    /**
     * Creates an invoice or prefactura.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload, ?InvoiceOptions $options = null): array
    {
        return $this->transport->post('/api/v1/invoices', $payload, ($options ?? new InvoiceOptions())->toQuery());
    }

    /**
     * Generates a QR for the provided prefactura IDs.
     *
     * @param list<string> $invoiceIds
     * @return array<string, mixed>
     */
    public function generateQr(array $invoiceIds, bool $publish = false): array
    {
        return $this->transport->post('/api/v1/invoices/bulk', [
            'ids' => $invoiceIds,
            'action' => 'bulk_generate_qr',
            'publish' => $publish,
        ]);
    }

    /**
     * Emits a prefactura as a final invoice.
     *
     * @return array<string, mixed>
     */
    public function emitPrefactura(string $originId): array
    {
        return $this->transport->post('/api/v1/clientfel/invoices', [
            'id_origin' => $originId,
        ]);
    }

    /**
     * Retrieves the status for a previously issued invoice.
     *
     * @return array<string, mixed>
     */
    public function status(string $originId): array
    {
        return $this->transport->post('/api/v1/clientfel/invoices/status', [
            'id_origin' => $originId,
        ]);
    }
}

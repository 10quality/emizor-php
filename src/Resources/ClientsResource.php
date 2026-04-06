<?php

declare(strict_types=1);

namespace Emizor\Resources;

use Emizor\Contracts\TransportInterface;

/**
 * Handles customer-related Emizor operations.
 */
final readonly class ClientsResource
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    /**
     * Creates a client in Emizor.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->transport->post('/api/v1/clients', $payload);
    }
}

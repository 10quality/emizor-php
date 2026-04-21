<?php

declare(strict_types=1);

namespace Emizor\Resources;

use Emizor\Contracts\TransportInterface;

/**
 * Handles product catalog operations.
 */
final readonly class ProductsResource
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    /**
     * Lists products with pagination and filtering.
     *
     * @return array<string, mixed>
     */
    public function list(int $page = 1, string $filter = ''): array
    {
        return $this->transport->get('/api/v1/products', [
            'page' => $page,
            'filter' => $filter,
        ]);
    }

    /**
     * Creates a product in Emizor.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->transport->post('/api/v1/products', $payload);
    }

    /**
     * Updates a product in Emizor.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function update(string $productId, array $payload): array
    {
        return $this->transport->put(sprintf('/api/v1/products/%s', rawurlencode($productId)), $payload);
    }

    /**
     * Deletes a product in Emizor.
     *
     * @return array<string, mixed>
     */
    public function delete(string $productId): array
    {
        return $this->transport->delete(sprintf('/api/v1/products/%s', rawurlencode($productId)));
    }
}

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
}

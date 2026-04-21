<?php

declare(strict_types=1);

namespace Emizor\Resources;

use Emizor\Contracts\TransportInterface;

/**
 * Handles parametric catalog operations.
 */
final readonly class ParametricasResource
{
    public function __construct(
        private TransportInterface $transport,
    ) {
    }

    /**
     * Lists one parametric catalog from Emizor.
     *
     * @return array<string, mixed>
     */
    public function list(string $parametrica): array
    {
        return $this->transport->get(sprintf(
            '/api/v1/clientfel/parametricas/%s',
            rawurlencode($parametrica),
        ));
    }
}

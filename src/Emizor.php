<?php

declare(strict_types=1);

namespace Emizor;

use Emizor\Contracts\TransportInterface;
use Emizor\Http\GuzzleTransport;
use Emizor\Resources\ClientsResource;
use Emizor\Resources\InvoicesResource;
use Emizor\Resources\ProductsResource;

/**
 * Main entry point for the Emizor PHP SDK.
 */
final class Emizor
{
    private readonly ClientsResource $clients;
    private readonly InvoicesResource $invoices;
    private readonly ProductsResource $products;

    public function __construct(
        private readonly TransportInterface $transport,
    ) {
        $this->clients = new ClientsResource($this->transport);
        $this->invoices = new InvoicesResource($this->transport);
        $this->products = new ProductsResource($this->transport);
    }

    /**
     * Creates the SDK using environment-backed configuration.
     */
    public static function fromEnvironment(): self
    {
        return new self(new GuzzleTransport(Config::fromEnvironment()));
    }

    /**
     * Creates the SDK using explicit configuration.
     */
    public static function fromConfig(Config $config): self
    {
        return new self(new GuzzleTransport($config));
    }

    /**
     * Verifies a customer NIT against the Emizor API.
     *
     * @return array<string, mixed>
     */
    public function verifyNit(string $nit): array
    {
        return $this->transport->get(sprintf('/api/v1/clientfel/verify_nit/%s', rawurlencode($nit)));
    }

    /**
     * Returns the clients resource.
     */
    public function clients(): ClientsResource
    {
        return $this->clients;
    }

    /**
     * Returns the invoices resource.
     */
    public function invoices(): InvoicesResource
    {
        return $this->invoices;
    }

    /**
     * Returns the products resource.
     */
    public function products(): ProductsResource
    {
        return $this->products;
    }
}

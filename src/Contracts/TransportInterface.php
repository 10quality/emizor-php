<?php

declare(strict_types=1);

namespace Emizor\Contracts;

/**
 * Defines the transport contract used by SDK resources.
 */
interface TransportInterface
{
    /**
     * Sends a GET request to the Emizor API.
     *
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function get(string $uri, array $query = []): array;

    /**
     * Sends a POST request to the Emizor API.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function post(string $uri, array $payload = [], array $query = []): array;
}

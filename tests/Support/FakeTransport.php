<?php

declare(strict_types=1);

namespace Emizor\Tests\Support;

use Emizor\Contracts\TransportInterface;

/**
 * Test double used to capture transport requests.
 */
final class FakeTransport implements TransportInterface
{
    /**
     * @var list<array{method: string, uri: string, payload: array<string, mixed>, query: array<string, mixed>}>
     */
    public array $requests = [];

    /**
     * @param array<string, mixed> $response
     */
    public function __construct(
        private array $response = ['success' => true],
    ) {
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function get(string $uri, array $query = []): array
    {
        $this->requests[] = [
            'method' => 'GET',
            'uri' => $uri,
            'payload' => [],
            'query' => $query,
        ];

        return $this->response;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function post(string $uri, array $payload = [], array $query = []): array
    {
        $this->requests[] = [
            'method' => 'POST',
            'uri' => $uri,
            'payload' => $payload,
            'query' => $query,
        ];

        return $this->response;
    }
}

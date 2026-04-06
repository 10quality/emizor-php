<?php

declare(strict_types=1);

namespace Emizor\Http;

use Emizor\Config;
use Emizor\Contracts\TransportInterface;
use Emizor\Exceptions\HttpException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Guzzle-based transport implementation for the Emizor API.
 */
final class GuzzleTransport implements TransportInterface
{
    private ClientInterface $client;

    public function __construct(
        private readonly Config $config,
        ?ClientInterface $client = null,
    ) {
        $this->client = $client ?? new Client([
            'base_uri' => rtrim($this->config->baseUrl, '/') . '/',
            'headers' => $this->config->headers(),
        ]);
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function get(string $uri, array $query = []): array
    {
        return $this->request('GET', $uri, [], $query);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function post(string $uri, array $payload = [], array $query = []): array
    {
        return $this->request('POST', $uri, $payload, $query);
    }

    /**
     * Sends the request and normalizes the JSON response.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    private function request(string $method, string $uri, array $payload, array $query): array
    {
        try {
            $options = [];
            if ($query !== []) {
                $options['query'] = $query;
            }
            if ($payload !== []) {
                $options['json'] = $payload;
            }

            return $this->decode($this->client->request($method, ltrim($uri, '/'), $options));
        } catch (GuzzleException $exception) {
            throw new HttpException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }

    /**
     * Decodes a JSON response body.
     *
     * @return array<string, mixed>
     */
    private function decode(ResponseInterface $response): array
    {
        $content = (string) $response->getBody();
        if ($content === '') {
            return [];
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new HttpException('The Emizor API returned an invalid JSON response.');
        }

        return $decoded;
    }
}

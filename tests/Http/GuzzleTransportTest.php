<?php

declare(strict_types=1);

namespace Emizor\Tests\Http;

use Emizor\Config;
use Emizor\Exceptions\HttpException;
use Emizor\Http\GuzzleTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class GuzzleTransportTest extends TestCase
{
    public function test_it_sends_get_requests(): void
    {
        $transport = new GuzzleTransport(
            new Config('https://felapp2.emizor.com', 'token', 'secret'),
            $this->clientFromMock(new MockHandler([
                new Response(200, [], json_encode(['success' => true])),
            ])),
        );

        $response = $transport->get('/api/v1/products', ['page' => 1]);

        self::assertSame(['success' => true], $response);
    }

    public function test_it_sends_post_requests(): void
    {
        $transport = new GuzzleTransport(
            new Config('https://felapp2.emizor.com', 'token', 'secret'),
            $this->clientFromMock(new MockHandler([
                new Response(200, [], json_encode(['data' => ['id' => 'abc']])),
            ])),
        );

        $response = $transport->post('/api/v1/clients', ['name' => 'Test']);

        self::assertSame(['data' => ['id' => 'abc']], $response);
    }

    public function test_it_returns_an_empty_array_for_empty_bodies(): void
    {
        $transport = new GuzzleTransport(
            new Config('https://felapp2.emizor.com', 'token', 'secret'),
            $this->clientFromMock(new MockHandler([
                new Response(204, [], ''),
            ])),
        );

        self::assertSame([], $transport->get('/api/v1/products'));
    }

    public function test_it_throws_for_invalid_json(): void
    {
        $transport = new GuzzleTransport(
            new Config('https://felapp2.emizor.com', 'token', 'secret'),
            $this->clientFromMock(new MockHandler([
                new Response(200, [], '"not-an-array"'),
            ])),
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('The Emizor API returned an invalid JSON response.');

        $transport->get('/api/v1/products');
    }

    public function test_it_wraps_guzzle_exceptions(): void
    {
        $request = new Request('GET', '/api/v1/products');
        $transport = new GuzzleTransport(
            new Config('https://felapp2.emizor.com', 'token', 'secret'),
            $this->clientFromMock(new MockHandler([
                new ConnectException('Network error', $request),
            ])),
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Network error');

        $transport->get('/api/v1/products');
    }

    private function clientFromMock(MockHandler $mockHandler): Client
    {
        return new Client([
            'handler' => HandlerStack::create($mockHandler),
            'http_errors' => false,
        ]);
    }
}

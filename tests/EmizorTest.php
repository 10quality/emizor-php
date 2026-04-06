<?php

declare(strict_types=1);

namespace Emizor\Tests;

use Emizor\Config;
use Emizor\Emizor;
use Emizor\Resources\ClientsResource;
use Emizor\Resources\InvoicesResource;
use Emizor\Resources\ProductsResource;
use Emizor\Tests\Support\FakeTransport;
use PHPUnit\Framework\TestCase;

final class EmizorTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('EMIZOR_BASE_URL');
        putenv('EMIZOR_API_TOKEN');
        putenv('EMIZOR_API_SECRET');
    }

    public function test_it_verifies_nit_using_the_transport(): void
    {
        $transport = new FakeTransport(['success' => true]);
        $sdk = new Emizor($transport);

        $response = $sdk->verifyNit('123 456');

        self::assertSame(['success' => true], $response);
        self::assertSame('/api/v1/clientfel/verify_nit/123%20456', $transport->requests[0]['uri']);
    }

    public function test_it_exposes_resources(): void
    {
        $sdk = new Emizor(new FakeTransport());

        self::assertInstanceOf(ClientsResource::class, $sdk->clients());
        self::assertInstanceOf(InvoicesResource::class, $sdk->invoices());
        self::assertInstanceOf(ProductsResource::class, $sdk->products());
    }

    public function test_it_can_be_built_from_environment(): void
    {
        putenv('EMIZOR_BASE_URL=https://felapp2.emizor.com');
        putenv('EMIZOR_API_TOKEN=token');
        putenv('EMIZOR_API_SECRET=secret');

        self::assertInstanceOf(Emizor::class, Emizor::fromEnvironment());
    }

    public function test_it_can_be_built_from_config(): void
    {
        $config = new Config('https://felapp2.emizor.com', 'token', 'secret');

        self::assertInstanceOf(Emizor::class, Emizor::fromConfig($config));
    }
}

<?php

declare(strict_types=1);

namespace Emizor\Tests\Resources;

use Emizor\Resources\ProductsResource;
use Emizor\Tests\Support\FakeTransport;
use PHPUnit\Framework\TestCase;

final class ProductsResourceTest extends TestCase
{
    public function test_it_lists_products(): void
    {
        $transport = new FakeTransport(['data' => []]);
        $resource = new ProductsResource($transport);

        $response = $resource->list(3, 'service');

        self::assertSame(['data' => []], $response);
        self::assertSame('/api/v1/products', $transport->requests[0]['uri']);
        self::assertSame([
            'page' => 3,
            'filter' => 'service',
        ], $transport->requests[0]['query']);
    }

    public function test_it_creates_products(): void
    {
        $transport = new FakeTransport(['data' => ['id' => 'product-1']]);
        $resource = new ProductsResource($transport);

        $response = $resource->create([
            'product_key' => 'Minerva',
            'price' => '440',
        ]);

        self::assertSame(['data' => ['id' => 'product-1']], $response);
        self::assertSame('POST', $transport->requests[0]['method']);
        self::assertSame('/api/v1/products', $transport->requests[0]['uri']);
    }

    public function test_it_updates_products(): void
    {
        $transport = new FakeTransport(['data' => ['id' => 'product-1']]);
        $resource = new ProductsResource($transport);

        $response = $resource->update('product-1', [
            'product_key' => 'Minerva Plus',
        ]);

        self::assertSame(['data' => ['id' => 'product-1']], $response);
        self::assertSame('PUT', $transport->requests[0]['method']);
        self::assertSame('/api/v1/products/product-1', $transport->requests[0]['uri']);
        self::assertSame(['product_key' => 'Minerva Plus'], $transport->requests[0]['payload']);
    }

    public function test_it_deletes_products(): void
    {
        $transport = new FakeTransport(['data' => ['is_deleted' => true]]);
        $resource = new ProductsResource($transport);

        $response = $resource->delete('product-1');

        self::assertSame(['data' => ['is_deleted' => true]], $response);
        self::assertSame('DELETE', $transport->requests[0]['method']);
        self::assertSame('/api/v1/products/product-1', $transport->requests[0]['uri']);
    }
}

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
}

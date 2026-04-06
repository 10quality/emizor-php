<?php

declare(strict_types=1);

namespace Emizor\Tests\Resources;

use Emizor\Resources\ClientsResource;
use Emizor\Tests\Support\FakeTransport;
use PHPUnit\Framework\TestCase;

final class ClientsResourceTest extends TestCase
{
    public function test_it_creates_clients(): void
    {
        $transport = new FakeTransport(['data' => ['id' => 'client-1']]);
        $resource = new ClientsResource($transport);

        $response = $resource->create(['name' => 'Demo']);

        self::assertSame(['data' => ['id' => 'client-1']], $response);
        self::assertSame('/api/v1/clients', $transport->requests[0]['uri']);
        self::assertSame(['name' => 'Demo'], $transport->requests[0]['payload']);
    }
}

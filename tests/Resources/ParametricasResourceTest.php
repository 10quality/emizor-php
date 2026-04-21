<?php

declare(strict_types=1);

namespace Emizor\Tests\Resources;

use Emizor\Resources\ParametricasResource;
use Emizor\Tests\Support\FakeTransport;
use PHPUnit\Framework\TestCase;

final class ParametricasResourceTest extends TestCase
{
    public function test_it_lists_parametricas(): void
    {
        $transport = new FakeTransport(['data' => []]);
        $resource = new ParametricasResource($transport);

        $response = $resource->list('tipos-documento-de-identidad');

        self::assertSame(['data' => []], $response);
        self::assertSame('GET', $transport->requests[0]['method']);
        self::assertSame(
            '/api/v1/clientfel/parametricas/tipos-documento-de-identidad',
            $transport->requests[0]['uri'],
        );
    }
}

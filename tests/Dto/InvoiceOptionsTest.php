<?php

declare(strict_types=1);

namespace Emizor\Tests\Dto;

use Emizor\Dto\InvoiceOptions;
use PHPUnit\Framework\TestCase;

final class InvoiceOptionsTest extends TestCase
{
    public function test_it_converts_to_query_parameters(): void
    {
        $options = new InvoiceOptions(false, true, false);

        self::assertSame([
            'should_emit' => 'false',
            'paid' => 'true',
            'send_mail' => 'false',
        ], $options->toQuery());
    }
}

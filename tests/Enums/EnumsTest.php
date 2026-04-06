<?php

declare(strict_types=1);

namespace Emizor\Tests\Enums;

use Emizor\Enums\CurrencyCode;
use Emizor\Enums\DocumentType;
use Emizor\Enums\PaymentMethod;
use Emizor\Enums\SectorDocumentType;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    public function test_document_type_enum_values(): void
    {
        self::assertSame('1', DocumentType::CI->value);
        self::assertSame('5', DocumentType::NIT->value);
    }

    public function test_payment_method_enum_values(): void
    {
        self::assertSame('5', PaymentMethod::CASH->value);
        self::assertSame('86', PaymentMethod::MIXED->value);
    }

    public function test_currency_enum_values(): void
    {
        self::assertSame('1', CurrencyCode::BOB->value);
    }

    public function test_sector_document_type_enum_values(): void
    {
        self::assertSame('1', SectorDocumentType::STANDARD_INVOICE->value);
    }
}

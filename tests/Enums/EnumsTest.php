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
        self::assertSame('2', DocumentType::CEX->value);
        self::assertSame('3', DocumentType::PAS->value);
        self::assertSame('4', DocumentType::OD->value);
        self::assertSame('5', DocumentType::NIT->value);
    }

    public function test_payment_method_enum_values(): void
    {
        self::assertCount(151, PaymentMethod::cases());
        self::assertSame('1', PaymentMethod::CASH->value);
        self::assertSame('86', PaymentMethod::MIXED->value);
        self::assertSame('295', PaymentMethod::AUTOMATIC_DEBIT->value);
        self::assertSame('8', PaymentMethod::ACCOUNT_DEPOSIT->value);
        self::assertSame('27', PaymentMethod::GIFT_CARD->value);
        self::assertSame('6', PaymentMethod::POST_PAYMENT->value);
        self::assertSame('7', PaymentMethod::BANK_TRANSFER->value);
        self::assertSame('9', PaymentMethod::SWIFT_TRANSFER->value);
        self::assertSame('4', PaymentMethod::VOUCHERS->value);
        self::assertSame('294', PaymentMethod::PAYMENT_CHANNEL_WALLET_ONLINE_PAYMENT->value);
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

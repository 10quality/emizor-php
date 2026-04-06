<?php

declare(strict_types=1);

namespace Emizor\Enums;

/**
 * Payment methods explicitly documented by Emizor.
 */
enum PaymentMethod: string
{
    case CASH = '5';
    case MIXED = '86';
}

<?php

declare(strict_types=1);

namespace Emizor\Enums;

/**
 * Identity document types accepted by Emizor.
 */
enum DocumentType: string
{
    case CI = '1';
    case CEX = '2';
    case PAS = '3';
    case OD = '4';
    case NIT = '5';
}

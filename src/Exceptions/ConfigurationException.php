<?php

declare(strict_types=1);

namespace Emizor\Exceptions;

use InvalidArgumentException;

/**
 * Thrown when required SDK configuration is missing.
 */
final class ConfigurationException extends InvalidArgumentException
{
}

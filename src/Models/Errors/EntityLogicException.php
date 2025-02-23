<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Errors;

use RuntimeException;

/**
 * Used inside Model when it detects inconsistent state
 */
class EntityLogicException extends RuntimeException
{
}

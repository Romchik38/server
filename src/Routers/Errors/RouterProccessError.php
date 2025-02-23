<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Errors;

use RuntimeException;

/**
 * throws during Router execute
 */
class RouterProccessError extends RuntimeException
{
}

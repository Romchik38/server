<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Errors;

use RuntimeException;

/**
 * throws during Router execute
 */
class RouterProccessErrorException extends RuntimeException
{
}

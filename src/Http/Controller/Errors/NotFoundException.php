<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Errors;

use RuntimeException;

/**
 * @internal
 *
 * Controller throws this exception when the path not found
 */
class NotFoundException extends RuntimeException
{
}

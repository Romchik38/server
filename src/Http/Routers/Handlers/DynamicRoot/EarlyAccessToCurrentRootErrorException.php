<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\DynamicRoot;

use RuntimeException;

class EarlyAccessToCurrentRootErrorException extends RuntimeException
{
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares\Result;

use Romchik38\Server\Http\Controller\Path;

interface PathMiddlewareResultInterface
{
    public function getPath(): Path;
}

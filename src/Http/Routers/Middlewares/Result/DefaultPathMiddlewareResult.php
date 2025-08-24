<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares\Result;

use Romchik38\Server\Http\Controller\Path;

class DefaultPathMiddlewareResult implements PathMiddlewareResultInterface
{
    public function __construct(
        public readonly Path $path
    ) {
    }

    public function getPath(): Path
    {
        return $this->path;
    }
}

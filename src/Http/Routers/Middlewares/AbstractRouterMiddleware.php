<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Romchik38\Server\Http\Controller\Middleware\RequestMiddlewareInterface;

abstract class AbstractRouterMiddleware implements RequestMiddlewareInterface
{
    private(set) ?RequestMiddlewareInterface $prev = null;
    private(set) ?RequestMiddlewareInterface $next = null;

    public function setNext(RequestMiddlewareInterface $middleware): void
    {
        $this->next = $middleware;
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Romchik38\Server\Http\Controller\Middleware\RequestMiddlewareInterface;

interface RouterMiddlewareInterface extends RequestMiddlewareInterface
{
    public function setNext(RouterMiddlewareInterface $middleware): void;
}

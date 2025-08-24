<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HandlerRouterMiddleware extends AbstractRouterMiddleware
{
    public function __construct(
        protected RequestHandlerInterface $handler,
        string $attributeName = 'handler_router_middleware'
    ) {
        parent::__construct($attributeName);
    }

    public function __invoke(ServerRequestInterface $request): mixed
    {
        return $this->handler->handle($request);
    }
}

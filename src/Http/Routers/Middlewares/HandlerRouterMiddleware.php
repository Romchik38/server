<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ServerRequestInterface;

class HandlerRouterMiddleware extends AbstractRouterMiddleware
{
    public function __construct(
        string $attributeName = 'handler_router_middleware'
    ) {
        parent::__construct($attributeName);
    }

    public function __invoke(ServerRequestInterface $request): mixed
    {
        return new TextResponse('hello world');
    }
}

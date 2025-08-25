<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\HandlerRouterMiddlewareTest;

use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Routers\Middlewares\AbstractRouterMiddleware;

final class NullMiddleware extends AbstractRouterMiddleware
{
    public function __construct(
        string $attributeName = 'null_middleware'
    ) {
        parent::__construct($attributeName);
    }

    public function __invoke(ServerRequestInterface $request): mixed
    {
        return null;
    }
}

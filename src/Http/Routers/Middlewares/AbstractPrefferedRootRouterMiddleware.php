<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

abstract class AbstractPrefferedRootRouterMiddleware extends AbstractRouterMiddleware
{
    public function __construct(
        string $attributeName = 'preffered_root_router_middleware'
    ) {
        parent::__construct($attributeName);
    }
}

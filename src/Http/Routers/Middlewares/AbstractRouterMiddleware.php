<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use InvalidArgumentException;
use Romchik38\Server\Http\Routers\Middlewares\VO\AttributeName;

abstract class AbstractRouterMiddleware implements RouterMiddlewareInterface
{
    protected readonly AttributeName $attributeName;

    private(set) ?RouterMiddlewareInterface $prev = null;
    private(set) ?RouterMiddlewareInterface $next = null;

    /** @throws InvalidArgumentException */
    public function __construct(
        string $attributeName
    ) {
        $this->attributeName = new AttributeName($attributeName);
    }

    public function setNext(RouterMiddlewareInterface $middleware): void
    {
        $this->next = $middleware;
    }

    public function getAttributeName(): string
    {
        return (string) $this->attributeName;
    }
}

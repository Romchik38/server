<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers;

interface HttpRouterInterface
{
    public const REQUEST_METHOD_GET        = 'GET';
    public const REQUEST_METHOD_POST       = 'POST';
    public const NOT_FOUND_MESSAGE         = 'Error 404 - Page not found';
}

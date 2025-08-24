<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

use function count;
use function explode;

abstract class AbstractPathRouterMiddleware extends AbstractRouterMiddleware
{
    public const ATTRIBUTE_NAME = 'path_router_middleware';

    /** @return array<int,string> */
    protected function createParts(ServerRequestInterface $request): array
    {
        // 0. define
        $uri   = $request->getUri();
        $path  = $uri->getPath();
        [$url] = explode('?', $path);

        // 1. parse url
        $parts = explode('/', $url);

        // two blank elements for /
        if (count($parts) === 2 && $parts[0] === '' && $parts[1] === '') {
            $parts = [''];
        }
        return $parts;
    }
}

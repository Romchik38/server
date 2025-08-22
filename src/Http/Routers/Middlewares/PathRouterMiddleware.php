<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Psr\Http\Message\ServerRequestInterface;

use function array_shift;
use function count;
use function explode;

class PathRouterMiddleware extends AbstractRouterMiddleware
{
    public const ATTRIBUTE_NAME = 'path_router_middleware';

    public function __invoke(ServerRequestInterface $request): mixed
    {
        // 0. define
        $uri   = $request->getUri();
        $path  = $uri->getPath();
        [$url] = explode('?', $path);

        // 1. parse url
        $elements = explode('/', $url);

        // two blank elements for /
        if (count($elements) === 2 && $elements[0] === '' && $elements[1] === '') {
            $elements = [''];
        }

        // delete first blank item
        array_shift($elements);

        return $elements;
    }

    public function getAttributeName(): string
    {
        return $this::ATTRIBUTE_NAME;
    }
}

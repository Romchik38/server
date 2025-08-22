<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Path;

use function count;
use function explode;

abstract class AbstractPathRouterMiddleware extends AbstractRouterMiddleware
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

        return $this->getPath($elements);
    }

    /** @param array<int,string> $elements */
    abstract protected function getPath(array $elements): Path;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Path;

class DefaultPathRouterMiddleware extends AbstractPathRouterMiddleware
{
    public function __construct(
        string $attributeName = 'default_path_router_middleware'
    ) {
        parent::__construct($attributeName);
    }

    public function __invoke(ServerRequestInterface $request): mixed
    {
        $parts = $this->createParts($request);
        // replace blank with root
        if ($parts[0] === '') {
            $parts[0] = ControllerInterface::ROOT_NAME;
        }

        return new Path($parts);
    }
}

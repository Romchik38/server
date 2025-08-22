<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Path;

class DefaultPathRouterMiddleware extends AbstractPathRouterMiddleware
{
    public function __construct(
        string $attributeName = 'default_path_router_middleware'
    ) {
        parent::__construct($attributeName);
    }

    protected function getPath(array $elements): Path
    {
        // replace blank with root
        if ($elements[0] === '') {
            $elements[0] = ControllerInterface::ROOT_NAME;
        }

        return new Path($elements);
    }
}

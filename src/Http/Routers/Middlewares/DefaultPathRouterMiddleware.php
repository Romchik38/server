<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Routers\Middlewares\Result\DefaultPathMiddlewareResult;

class DefaultPathRouterMiddleware extends AbstractPathRouterMiddleware
{
    public function __construct(
        string $attributeName = 'default_path_router_middleware'
    ) {
        parent::__construct($attributeName);
    }

    /** @return DefaultPathMiddlewareResult|null */
    public function __invoke(ServerRequestInterface $request): mixed
    {
        $parts = $this->createParts($request);
        // replace blank with root
        if ($parts[0] === '') {
            $parts[0] = ControllerInterface::ROOT_NAME;
        }

        try {
            $path = Path::fromEncodedUrlParts($parts);
        } catch (InvalidArgumentException) {
            return null;
        }

        return new DefaultPathMiddlewareResult($path);
    }
}

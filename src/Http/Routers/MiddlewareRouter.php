<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Romchik38\Server\Http\Controller\Middleware\RequestMiddlewareInterface;
use RuntimeException;

class MiddlewareRouter implements RequestHandlerInterface
{
    public function __construct(
        private readonly RequestMiddlewareInterface $middleware
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $currentMiddleware = $this->middleware;
        $updatedRequest    = $request;

        while (true) {
            if ($currentMiddleware === null) {
                throw new RuntimeException('Router middleware does not return a response');
            }
            $result = $currentMiddleware($updatedRequest);
            if ($result instanceof ResponseInterface) {
                return $result;
            } elseif ($result !== null) {
                $updatedRequest = $request->withAttribute(
                    $currentMiddleware->getAttributeName(),
                    $result
                );
            }
            $currentMiddleware = $currentMiddleware->next;
        }
    }
}

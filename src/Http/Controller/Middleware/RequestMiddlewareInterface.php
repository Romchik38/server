<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestMiddlewareInterface
{
    public const TYPE = 'request_middleware';
    /**
     * Does own job and make a decision:
     *   - stop execution and return a response
     *      OR
     *   - continue execution and return null
     */
    public function __invoke(ServerRequestInterface $request): ?ResponseInterface;
}

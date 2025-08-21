<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Middleware;

use Psr\Http\Message\ServerRequestInterface;

interface RequestMiddlewareInterface
{
    public const TYPE = 'request_middleware';
    /**
     * Expected returns:
     *   - null - to pass exec to the next handler with no changes
     *   - `ResponseInterface` - to stop exec and return a response
     *   - other type - to pass exec to the next handler with this data
     */
    public function __invoke(ServerRequestInterface $request): mixed;

    /** To associate middleware with data obtained from __invoke */
    public function getAttributeName(): string;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers\Middleware;

use Psr\Http\Message\ResponseInterface;

interface RequestMiddlewareInterface
{
    /**
     * Does own job and make a decision:
     *   - stop execution and return a response
     *      OR
     *   - continue execution and return null
     */
    public function __invoke(): ?ResponseInterface;
}

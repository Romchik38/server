<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Middleware;

use Psr\Http\Message\ResponseInterface;

interface ResponseMiddlewareInterface
{
    public const TYPE = 'response_middleware';
    /**
     * Modify a response after action execution
     */
    public function __invoke(ResponseInterface $response): ResponseInterface;
}

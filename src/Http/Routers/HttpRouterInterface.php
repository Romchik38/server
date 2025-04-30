<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers;

use Psr\Http\Message\ResponseInterface;

interface HttpRouterInterface
{
    public const REQUEST_METHOD_GET        = 'GET';
    public const REQUEST_METHOD_POST       = 'POST';
    public const NOT_FOUND_CONTROLLER_NAME = 'not-found';
    public const NOT_FOUND_MESSAGE         = 'Error 404 - Page not found';

    public function execute(): ResponseInterface;
}

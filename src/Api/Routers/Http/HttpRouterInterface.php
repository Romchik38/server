<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Routers\Http;

use Psr\Http\Message\ResponseInterface;

interface HttpRouterInterface
{
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';

    const NOT_FOUND_CONTROLLER_NAME = 'not-found';
    const NOT_FOUND_MESSAGE = 'Error 404 - Page not found';

    public function execute(): ResponseInterface;
}

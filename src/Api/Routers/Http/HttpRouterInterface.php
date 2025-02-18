<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Routers\Http;

use Psr\Http\Message\ResponseInterface;

interface HttpRouterInterface
{
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';

    public function execute(): ResponseInterface;
}

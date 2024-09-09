<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Routers\Http;

use Romchik38\Server\Api\Routers\RouterInterface;
use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;

interface HttpRouterInterface extends RouterInterface
{
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';

    public function execute(): HttpRouterResultInterface;
}

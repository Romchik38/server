<?php

namespace Romchik38\Server\Api\Router\Http;

use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;

interface RouterHeadersInterface {
    public function setHeaders(HttpRouterResultInterface $result, array $path): void;
}
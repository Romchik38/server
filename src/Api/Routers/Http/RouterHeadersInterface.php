<?php

namespace Romchik38\Server\Api\Routers\Http;

use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;

interface RouterHeadersInterface
{
    public function getPath(): string;
    public function getMethod(): string;
    public function setHeaders(HttpRouterResultInterface $result, array $path): void;
}

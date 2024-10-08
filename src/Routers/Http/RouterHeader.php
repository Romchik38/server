<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Romchik38\Server\Api\Routers\Http\RouterHeadersInterface;

abstract class RouterHeader implements RouterHeadersInterface
{
    public function __construct(
        protected readonly string $path,
        protected readonly string $method
    ) {}

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}

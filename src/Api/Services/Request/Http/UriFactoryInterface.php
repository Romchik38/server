<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Request\Http;

interface UriFactoryInterface
{
    public function create(
        string $scheme,
        string $host
    ): UriInterface;
}

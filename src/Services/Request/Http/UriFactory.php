<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\UriFactoryInterface;
use Romchik38\Server\Api\Services\Request\Http\UriInterface;

class UriFactory implements UriFactoryInterface
{
    public function create(
        string $scheme,
        string $host,
        string $path
    ): UriInterface {
        return new Uri($scheme, $host, $path);
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\UriFactoryInterface;
use Romchik38\Server\Api\Services\Request\Http\UriInterface;

class UriFactory implements UriFactoryInterface
{
    public function create(string $scheme, string $host): UriInterface
    {
        return new Uri($scheme, $host);
    }
}

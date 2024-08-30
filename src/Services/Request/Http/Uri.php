<?php

namespace Romchik38\Server\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\UriInterface;

class Uri implements UriInterface
{
    protected readonly array $schemeList = ['http', 'https'];

    protected readonly string $scheme;

    public function __construct(
        string $scheme,
        protected readonly string $host
    ) {
        $this->scheme = strtolower($scheme);
    }

    public function getScheme(): string {
        return $this->scheme;
    }

    public function getHost(): string {
        return $this->host;
    }
}

<?php

namespace Romchik38\Server\Services\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\UriInterface;

class Uri implements UriInterface
{

    protected readonly string $scheme;
    protected readonly string $host;

    public function __construct(
        string $scheme,
        string $host
    ) {
        $this->scheme = strtolower(str_replace(':', '', $scheme));
        $this->host = strtolower($host);
    }

    public function getScheme(): string {
        return $this->scheme;
    }

    public function getHost(): string {
        return $this->host;
    }
}

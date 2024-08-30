<?php

namespace Romchik38\Server\Services\Request\Http;

use Romchik38\Server\Api\Services\Request\Http\RequestInterface;
use Romchik38\Server\Api\Services\Request\Http\UriFactoryInterface;
use Romchik38\Server\Api\Services\Request\Http\UriInterface;

class Request implements RequestInterface
{
    public function __construct(
        protected readonly UriFactoryInterface $uriFactory
    ) {}
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri(): UriInterface
    {
        $scheme = $_SERVER['REQUEST_SCHEME'] ?? '';
        $host = $_SERVER['SERVER_NAME'] ?? '';
        return $this->uriFactory->create($scheme, $host);
    }
}

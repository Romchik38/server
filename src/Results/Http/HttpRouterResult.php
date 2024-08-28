<?php

declare(strict_types=1);

namespace Romchik38\Server\Results\Http;

use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;

class HttpRouterResult implements HttpRouterResultInterface
{
    public function __construct(
        protected string $response = HttpRouterResultInterface::DEFAULT_RESPONSE,
        protected array $headers = HttpRouterResultInterface::DEFAULT_HEADERS,
        protected int $statusCode = HttpRouterResultInterface::DEFAULT_STATUS_CODE,
    )
    {
    }
    public function getResponse(): string
    {
        return $this->response;
    }
    public function getHeaders(): array
    {
        return $this->headers;
    }
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setResponse(string $response): HttpRouterResultInterface
    {
        $this->response = $response;
        return $this;
    }

    public function setHeaders(array $headers): HttpRouterResultInterface
    {
        $this->headers = $headers;
        return $this;
    }
    public function setStatusCode(int $statusCode): HttpRouterResultInterface
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}

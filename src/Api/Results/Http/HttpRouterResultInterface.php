<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Results\Http;

use Romchik38\Server\Api\Results\RouterResultInterface;

interface HttpRouterResultInterface extends RouterResultInterface {
    const DEFAULT_RESPONSE = '';
    const DEFAULT_HEADERS = [];
    const DEFAULT_STATUS_CODE = 0;

    public function getResponse(): string;
    public function getHeaders(): array;
    public function getStatusCode(): int;

    public function setResponse(string $response): HttpRouterResultInterface;
    public function setHeaders(array $headers): HttpRouterResultInterface;
    public function setStatusCode(int $statusCode): HttpRouterResultInterface;
}
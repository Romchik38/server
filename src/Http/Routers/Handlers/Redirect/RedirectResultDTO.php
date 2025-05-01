<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

class RedirectResultDTO implements RedirectResultDTOInterface
{
    public function __construct(
        protected readonly string $uri,
        protected readonly int $statusCode
    ) {
    }

    public function getRedirectLocation(): string
    {
        return $this->uri;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

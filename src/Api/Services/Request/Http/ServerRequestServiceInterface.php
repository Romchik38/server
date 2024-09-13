<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Request\Http;

interface ServerRequestServiceInterface
{
    public function getBodyContent(): array|null;
    public function getRequestHeaders(): array|bool;
}

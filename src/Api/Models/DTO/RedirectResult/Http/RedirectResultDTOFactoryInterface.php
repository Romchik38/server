<?php

namespace Romchik38\Server\Api\Models\DTO\RedirectResult\Http;

interface RedirectResultDTOFactoryInterface
{
    public function create(string $uri, int $statusCode): RedirectResultDTOInterface;
}

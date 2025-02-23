<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\RedirectResult\Http;

interface RedirectResultDTOFactoryInterface
{
    public function create(string $uri, int $statusCode): RedirectResultDTOInterface;
}

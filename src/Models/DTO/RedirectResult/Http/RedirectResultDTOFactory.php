<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\RedirectResult\Http;

use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;

class RedirectResultDTOFactory implements RedirectResultDTOFactoryInterface
{
    public function create(string $uri, int $statusCode): RedirectResultDTOInterface
    {
        return new RedirectResultDTO($uri, $statusCode);
    }
}

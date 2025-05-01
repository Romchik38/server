<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

class RedirectResultDTOFactory implements RedirectResultDTOFactoryInterface
{
    public function create(string $uri, int $statusCode): RedirectResultDTOInterface
    {
        return new RedirectResultDTO($uri, $statusCode);
    }
}

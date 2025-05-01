<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

interface RedirectResultDTOFactoryInterface
{
    public function create(string $uri, int $statusCode): RedirectResultDTOInterface;
}

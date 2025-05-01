<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\NoSuchRedirectException;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\RepositoryException;

interface RepositoryInterface
{
    /**
     * @throws NoSuchRedirectException
     * @throws RepositoryException - On database error.
     */
    public function checkUrl(string $redirectFrom, string $method): RedirectModelInterface;
}

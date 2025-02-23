<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Redirect;

use Romchik38\Server\Api\Models\Redirect\RedirectModelInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

interface RedirectRepositoryInterface
{
    /**
     * Return a redirectModel entity or throws an error
     *
     * @throws NoSuchEntityException
     */
    public function checkUrl(string $redirectFrom, string $method): RedirectModelInterface;
}

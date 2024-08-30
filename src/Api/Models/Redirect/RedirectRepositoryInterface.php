<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Redirect;

use Romchik38\Server\Api\Models\Redirect\RedirectModelInterface;
use Romchik38\Server\Api\Models\RepositoryInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

interface RedirectRepositoryInterface extends RepositoryInterface
{
    /**
     * Return a redirectModel entity or throws an error
     * 
     * @throws NoSuchEntityException
     * @return RedirectModelInterface
     */
    public function checkUrl(string $url, string $method): RedirectModelInterface;
}

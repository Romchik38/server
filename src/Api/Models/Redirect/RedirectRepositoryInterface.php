<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models;

use Romchik38\Server\Api\Models\RedirectModelInterface;

interface RedirectRepositoryInterface extends RepositoryInterface
{
    public function checkUrl(string $url, string $method): RedirectModelInterface;
}

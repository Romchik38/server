<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models;

use Romchik38\Server\Api\Models\ModelInterface;

interface RedirectModelInterface extends ModelInterface
{
    public function getRedirectTo(): string;
    public function getRedirectCode(): int;
}

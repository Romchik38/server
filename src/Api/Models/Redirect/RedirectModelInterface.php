<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Redirect;

use Romchik38\Server\Api\Models\ModelInterface;

interface RedirectModelInterface extends ModelInterface
{
    public function getRedirectFrom(): string;
    public function getRedirectTo(): string;
    public function getRedirectCode(): int;
    public function getRedirectMethod(): string;
}

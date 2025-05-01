<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

interface RedirectModelInterface
{
    public function getRedirectFrom(): string;

    public function getRedirectTo(): string;

    public function getRedirectCode(): int;

    public function getRedirectMethod(): string;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Redirect\Http;

interface RedirectInterface 
{
    public function isRedirect(): bool;
    public function getRedirectLocation(): string;
    public function getStatusCode(): int;
    public function execute(string $url, string $method): void;
}
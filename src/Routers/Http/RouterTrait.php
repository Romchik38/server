<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

trait RouterTrait
{
    protected function normalizeRedirectUrl(
        string $url,
        string $host,
        string $scheme
    ): string
    {
        if(str_starts_with($url, 'http') || str_starts_with($url, 'https')){
            return $url;
        }
        if(str_starts_with($url, '/')){
            return sprintf(
                '%s://%s%s',
                $scheme,
                $host,
                $url
            );
        }
        return sprintf(
            '%s://%s/%s',
            $scheme,
            $host,
            $url
        );
    }
}
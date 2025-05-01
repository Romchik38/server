<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

interface RedirectResultDTOInterface
{
    public const REDIRECT_LOCATION_FIELD    = 'redirect_location';
    public const REDIRECT_STATUS_CODE_FIELD = 'status_code';

    public function getRedirectLocation(): string;

    public function getStatusCode(): int;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\CantCreateRedirectException;

interface RedirectInterface
{
    public const string SCHEME_HOST_DELIMITER = '://';
    public const ALLOWED_SCHEMAS              = ['http', 'https'];

    /** @throws CantCreateRedirectException */
    public function execute(
        string $redirectFrom,
        string $method
    ): RedirectResultDTOInterface|null;
}

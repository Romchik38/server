<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Redirect\Http;

use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;

interface RedirectInterface
{
    public const string SCHEME_HOST_DELIMITER = '://';
    public const ALLOWED_SCHEMAS              = ['http', 'https'];

    public function execute(string $redirectFrom, string $method): RedirectResultDTOInterface|null;
}

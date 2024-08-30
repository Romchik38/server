<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Redirect\Http;

use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;

interface RedirectInterface 
{
    const string SCHEME_HOST_DELIMITER = '://';

    public function execute(string $url, string $method): RedirectResultDTOInterface|null;
}
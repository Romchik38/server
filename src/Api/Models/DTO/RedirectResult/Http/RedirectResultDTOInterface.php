<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\RedirectResult\Http;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

/**
 * used in RedirectInterface
 * between RedirectModelInterface and HttpRouterResultInterface
 */
interface RedirectResultDTOInterface extends DTOInterface
{
    const REDIRECT_LOCATION_FIELD    = 'redirect_location';
    const REDIRECT_STATUS_CODE_FIELD = 'status_code';

    /** A full uri for the Location header */
    public function getRedirectLocation(): string;

    public function getStatusCode(): int;
}

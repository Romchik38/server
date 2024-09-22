<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services;

use Romchik38\Server\Api\Models\ModelInterface;

interface SessionInterface extends ModelInterface
{
    const SESSION_MAX_TIME_TO_LOGOUT = 86400;

    /**
     * Destroy a session
     */
    public function logout(): void;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services;

interface SessionInterface {
    const SESSION_USER_ID_FIELD = 'user_id';
    const SESSION_MAX_TIME_TO_LOGOUT = 86400; 

    /**
     * Get user id if it was provided
     * @return int [0 if there isn't user id]
     */
    public function getUserId(): int;

    /**
     * Destroy a session
     */
    public function logout(): void;

    /**
     * Set User Id
     */
    public function setUserId(int $id): void;
}
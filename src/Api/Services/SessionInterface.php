<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services;

interface SessionInterface
{
    public const SESSION_MAX_TIME_TO_LOGOUT = 86400;

    /**
     * Destroy a session
     */
    public function logout(): void;

    /** @return array<string,string> */
    public function getAllData(): array;

    public function getData(string $key): ?string;

    public function setData(string $key, string $value): SessionInterface;
}

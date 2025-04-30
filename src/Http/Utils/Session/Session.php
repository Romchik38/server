<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Session;

use function session_destroy;
use function session_id;
use function session_name;
use function session_start;
use function setcookie;
use function time;

class Session implements SessionInterface
{
    protected int $maxTimeToLogout = SessionInterface::SESSION_MAX_TIME_TO_LOGOUT;

    public function __construct()
    {
        session_start();
        $sessionId = session_id();
        if (($sessionId === false) || ($sessionId === '')) {
            throw new SessionDoesnWorkException('Session does\'t work correctly');
        }
    }

    public function getData(string $key): ?string
    {
        return $_SESSION[$key] ?? null;
    }

    public function getAllData(): array
    {
        return $_SESSION;
    }

    public function logout(): void
    {
        $_SESSION = [];
        $name     = session_name();
        if ($name !== false) {
            setcookie($name, '', time() - $this->maxTimeToLogout, '/');
        }
        session_destroy();
    }

    public function setData(string $key, string $value): SessionInterface
    {
        $_SESSION[$key] = $value;
        return $this;
    }
}

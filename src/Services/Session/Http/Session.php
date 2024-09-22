<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Session\Http;

use \Romchik38\Server\Api\Services\SessionInterface;
use \Romchik38\Server\Services\Errors\SessionDoesnWorkException;

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

    public function getData(string $key): mixed
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
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - $this->maxTimeToLogout, '/');
        }
        session_destroy();
    }

    public function setData(string $key, mixed $value): SessionInterface
    {
        $_SESSION[$key] = $value;
        return $this;
    }
}

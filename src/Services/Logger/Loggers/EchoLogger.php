<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Logger\Loggers;

use Romchik38\Server\Services\Logger\AbstractLogger;

class EchoLogger extends AbstractLogger
{
    public function sendAllLogs(): void
    {
    }

    protected function write(string $level, string $message)
    {
        echo 'Log: ' . $level, ' - ' . $message;
    }
}

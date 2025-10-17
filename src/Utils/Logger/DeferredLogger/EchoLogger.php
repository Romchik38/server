<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Logger\DeferredLogger;

use Romchik38\Server\Utils\Logger\AbstractLogger;

class EchoLogger extends AbstractLogger implements DeferredLoggerInterface
{
    public function sendAllLogs(): void
    {
    }

    protected function write(string $level, string $message): void
    {
        echo 'Log: ' . $level, ' - ' . $message;
    }
}

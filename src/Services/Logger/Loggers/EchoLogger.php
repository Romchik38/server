<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Logger\Loggers;

use Romchik38\Server\Services\Logger\Logger;

class EchoLogger extends Logger {

    public function sendAllLogs(): void {

    }

    protected function write(string $level, string $message) {
        echo 'Log: ' . $level, ' - ' . $message;
    }
}

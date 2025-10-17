<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Logger\DeferredLogger;

interface DeferredLoggerInterface
{
    /**
     * Method, that write all logs to external service if they are not sent at the time
     * If you will send each message when log() is called, just make this sendAllLogs() as a stub
     */
    public function sendAllLogs(): void;
}

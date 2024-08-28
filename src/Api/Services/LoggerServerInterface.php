<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services;

use Psr\Log\LoggerInterface;

interface LoggerServerInterface extends LoggerInterface{
    
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    
    /**
     * Method, that write all logs to external service if they are not sent at the time
     * If you will send each message when log() is called, just make this sendAllLogs() as a stub
     *
     * @return void
     */
    public function sendAllLogs(): void;
}
<?php

namespace Romchik38\Server\Services\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Romchik38\Server\Api\Services\LoggerServerInterface;
use Psr\Log\LoggerInterface;

abstract class Logger extends AbstractLogger implements LoggerServerInterface
{
    protected array $messages = [];

    public function __construct(
        protected readonly int $logLevel,
        protected LoggerServerInterface|null $alternativeLogger = null
    ) {
    }

    protected array $levels = [
        LogLevel::EMERGENCY => 0, //       Emergency: system is unusable
        LogLevel::ALERT => 1,     //       Alert: action must be taken immediately
        LogLevel::CRITICAL => 2,  //       Critical: critical conditions
        LogLevel::ERROR => 3,     //       Error: error conditions
        LogLevel::WARNING => 4,   //       Warning: warning conditions
        LogLevel::NOTICE => 5,    //       Notice: normal but significant condition
        LogLevel::INFO => 6,      //       Informational: informational messages
        LogLevel::DEBUG => 7      //       Debug: debug-level messages
    ];

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if ($this->logLevel < $this->levels[$level]) {
            return;
        }

        $interpolaitedMessage = $this->interpolate($message, $context);
        $this->write($level, $interpolaitedMessage);
    }

    /**
     * Use this method to write log to external service.
     * 
     * Also you can push all messages to $messages array and then, 
     * in the finish line, send all messages. LoggerServerInterface has sendAllLogs() for this.
     *
     * @param string $level
     * @param string $message
     * @return void
     */
    abstract protected function write(string $level, string $message);

    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /** 
     * Write logs to another logger when main logger doesn't work
     * 
     * @param array $writeMessages [['level', 'message'], ...]
     */
    protected function sendAllToalternativeLog(array $writeMessages): void {
        foreach($writeMessages as $item) {
            [$level, $message] = $item;
            $this->alternativeLogger->log($level, $message);
        }
    }
}

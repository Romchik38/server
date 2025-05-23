<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Logger;

use Psr\Log\AbstractLogger as PsrAbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

use function is_array;
use function is_object;
use function method_exists;
use function strtr;

abstract class AbstractLogger extends PsrAbstractLogger
{
    /** @var array <int,array<int,string>> $messages */
    protected array $messages = [];

    public function __construct(
        protected readonly int $logLevel,
        protected LoggerInterface|null $alternativeLogger = null
    ) {
    }

    /** @var array<string,int> $levels */
    protected array $levels = [
        LogLevel::EMERGENCY => 0, //       Emergency: system is unusable
        LogLevel::ALERT     => 1, //       Alert: action must be taken immediately
        LogLevel::CRITICAL  => 2, //       Critical: critical conditions
        LogLevel::ERROR     => 3, //       Error: error conditions
        LogLevel::WARNING   => 4, //       Warning: warning conditions
        LogLevel::NOTICE    => 5, //       Notice: normal but significant condition
        LogLevel::INFO      => 6, //       Informational: informational messages
        LogLevel::DEBUG     => 7, //       Debug: debug-level messages
    ];

    /**
     * @param mixed $level
     */
    public function log($level, string|Stringable $message, array $context = []): void
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
     * @return void
     */
    abstract protected function write(string $level, string $message);

    /**
     * @param array<int|string,mixed> $context
     * */
    protected function interpolate(
        string|Stringable $message,
        array $context = []
    ): string {
        // build a replacement array with braces around the context keys
        $replace = [];
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (! is_array($val) && (! is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr((string) $message, $replace);
    }

    /**
     * Write logs to another logger when main logger doesn't work
     *
     * @param array<array<int,string>> $writeMessages  - [['level', 'message'], ...]
     */
    protected function sendAllToalternativeLog(array $writeMessages): void
    {
        if ($this->alternativeLogger === null) {
            return;
        }
        foreach ($writeMessages as $item) {
            [$level, $message] = $item;
            $this->alternativeLogger->log($level, $message);
        }
    }
}

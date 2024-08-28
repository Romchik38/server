<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Logger\Loggers;

use Romchik38\Server\Services\Logger\Logger;
use Romchik38\Server\Api\Services\Loggers\FileLoggerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Romchik38\Server\Api\Services\LoggerServerInterface;

class FileLogger extends Logger implements FileLoggerInterface
{
    protected readonly string $fullFilePath;

    /**
     * @param string $protocol [ file:// (default), http://, ftp:// etc.]
     */
    public function __construct(
        string $fileName,
        int $logLevel,
        string $protocol = FileLoggerInterface::DEFAULT_PROTOCOL,
        protected readonly bool $useIncludePath = false,
        protected $context = null,
        protected LoggerServerInterface|null $alternativeLogger = null
    ) {
        parent::__construct($logLevel, $alternativeLogger);
        $this->fullFilePath = $protocol . $fileName;
    }

    public function write(string $level, string $message)
    {
        $this->messages[] = [$level, $message];
    }

    // protected function sendAllToalternativeLog(array $writeMessages): void {
    //     foreach($writeMessages as $item) {
    //         [$level, $message] = $item;
    //         $this->alternativeLogger->log($level, $message);
    //     }
    // }

    public function sendAllLogs(): void
    {
        if (count($this->messages) === 0) {
            return;
        }
        
        // 1 open file - write, pointer at the and, if the file doesn't exist, if will be created
        $fp = fopen($this->fullFilePath, 'a', $this->useIncludePath, $this->context);
        if ($fp === false) {
            // log error to alternative logger
            if ($this->alternativeLogger) {
                $this->alternativeLogger->log(LogLevel::ALERT, 'Can\'t open file to log: ' . $this->fullFilePath );
                $this->sendAllToalternativeLog($this->messages);
                $this->alternativeLogger->sendAllLogs();
            }
            return;
        }
        // 2 write
        $writeErrors = [];
        $date = new \DateTime();
        $dateString = $date->format(LoggerServerInterface::DATE_TIME_FORMAT);
        foreach($this->messages as $item) {
            [$level, $message] = $item;
            $str = '[' . $dateString . '] ' . $level . ': ' . $message . PHP_EOL;
            $writeResult = fwrite($fp, $str);
            if ($writeResult === false) {
                $writeErrors[] = $item;
            }
        }
        if (count($writeErrors) > 0) {
            // log error to alternative logger
            if ($this->alternativeLogger) {
                $this->alternativeLogger->log(LogLevel::ALERT, 'Some logs not saved to file: ' . $this->fullFilePath );
                $this->sendAllToalternativeLog($writeErrors);
            }
        }
        // 3 close
        $closeResult = fclose($fp);
        if ($closeResult === false) {
            // log error to alternative logger
            if ($this->alternativeLogger) {
                $this->alternativeLogger->log(LogLevel::ALERT, 'Can\'t close file: ' . $this->fullFilePath );
                $this->sendAllToalternativeLog($this->messages);
            }
        }
    }
}

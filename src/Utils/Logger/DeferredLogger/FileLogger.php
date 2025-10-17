<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Logger\DeferredLogger;

use Psr\Log\LogLevel;
use Romchik38\Server\Utils\Logger\AbstractFileLogger;

use function count;
use function fclose;
use function fopen;
use function fwrite;

class FileLogger extends AbstractFileLogger implements DeferredLoggerInterface
{
    public function write(string $level, string $message): void
    {
        $this->messages[] = [$level, $message];
    }

    public function sendAllLogs(): void
    {
        if (count($this->messages) === 0) {
            return;
        }

        // 1 open file - write, pointer at the end, if the file doesn't exist, if will be created
        $fp = fopen($this->fullFilePath, 'a', $this->useIncludePath, $this->context);
        if ($fp === false) {
            // log error to alternative logger
            if ($this->alternativeLogger !== null) {
                $this->alternativeLogger->log(LogLevel::ALERT, 'Can\'t open file to log: ' . $this->fullFilePath);
                foreach ($this->messages as $item) {
                    [$level, $message] = $item;
                    $this->alternativeLogger->log($level, $message);
                }
            }
            return;
        }
        // 2 write
        $writeErrors = [];
        foreach ($this->messages as $item) {
            [$level, $message] = $item;
            $str               = $this->createLine($level, $message);
            $writeResult       = fwrite($fp, $str);
            if ($writeResult === false) {
                $writeErrors[] = $item;
            }
        }
        if (count($writeErrors) > 0) {
            // log error to alternative logger
            if ($this->alternativeLogger) {
                $this->alternativeLogger->log(LogLevel::ALERT, 'Some logs not saved to file: ' . $this->fullFilePath);
                $this->sendAllToalternativeLog($writeErrors);
            }
        }
        // 3 close
        $closeResult = fclose($fp);
        if ($closeResult === false) {
            // log error to alternative logger
            if ($this->alternativeLogger) {
                $this->alternativeLogger->log(LogLevel::ALERT, 'Can\'t close file: ' . $this->fullFilePath);
                $this->sendAllToalternativeLog($this->messages);
            }
        }
    }
}

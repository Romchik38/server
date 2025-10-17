<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Logger;

use Psr\Log\LogLevel;

use function fclose;
use function fopen;
use function fwrite;

class FileLogger extends AbstractFileLogger
{
    protected function write(string $level, string $message): void
    {
        $fp = fopen($this->fullFilePath, 'a', $this->useIncludePath, $this->context);
        if ($fp === false) {
            // log error to alternative logger
            if ($this->alternativeLogger !== null) {
                $this->alternativeLogger->log(LogLevel::ALERT, 'Can\'t open file to log: ' . $this->fullFilePath);
                $this->alternativeLogger->log($level, $message);
            }
            return;
        }
        $line        = $this->createLine($level, $message);
        $writeResult = fwrite($fp, $line);
        if ($writeResult === false) {
            if ($this->alternativeLogger !== null) {
                $this->alternativeLogger->log(
                    LogLevel::ALERT,
                    'Some logs not saved to file: ' . $this->fullFilePath
                );
                $this->alternativeLogger->log($level, $message);
            }
        }
        fclose($fp);
    }
}

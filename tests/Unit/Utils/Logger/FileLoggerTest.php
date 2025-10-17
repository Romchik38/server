<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Logger;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Romchik38\Server\Utils\Logger\FileLogger;

use function file_exists;
use function file_get_contents;
use function str_contains;
use function unlink;

final class FileLoggerTest extends TestCase
{
    public function testWrite(): void
    {
        // Prepare
        $filename = __DIR__ . '/testfile.log';
        if (file_exists($filename) === true) {
            unlink($filename);
        }

        // Process
        $logger = new FileLogger($filename, 4);

        $message  = 'test log';
        $logLevel = LogLevel::ERROR;

        $logger->log($logLevel, $message);

        // Check
        $text = file_get_contents($filename);

        $this->assertTrue(str_contains($text, $message));
    }
}

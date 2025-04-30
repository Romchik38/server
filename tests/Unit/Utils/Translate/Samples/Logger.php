<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Translate\Samples;

use Psr\Log\LoggerInterface;
use Stringable;

final class Logger implements LoggerInterface
{
    public function __construct(
        public mixed $level = 0,
        public string $message = ''
    ) {
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
    }

    public function error(string|Stringable $message, array $context = []): void
    {
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
    }

    public function info(string|Stringable $message, array $context = []): void
    {
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
    }

    /** @param mixed $level */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->level   = $level;
        $this->message = $message;
    }
}

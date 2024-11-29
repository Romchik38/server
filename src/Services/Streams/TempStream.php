<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Streams;

class TempStream implements TempStreamInterface
{
    /** @var resource $fp */
    protected $fp;
    protected const PROTOCOL = 'php://temp';
    protected const MODE = 'rw';
    protected bool $isClosed = false;

    public function __construct()
    {
        $fp = fopen($this::PROTOCOL, $this::MODE);
        if ($fp === false) {
            throw new StreamProcessException('Cannot open stream to write data');
        }
        $this->fp = $fp;
    }

    public function write(string $data): void
    {
        $result = fwrite($this->fp, $data);
        if ($result === false) {
            throw new StreamProcessException('Cannot write data to stream');
        }
    }

    public function writeFromCallable(
        callable $fn,
        int $resourceIndex,
        ...$args
    ): void {
        $args[$resourceIndex] = $this->fp;

        try {
            $result = $fn(...$args);

            if ($result === false) {
                throw new StreamProcessException('Error during callable execution');
            }
        } catch (\Exception $e) {
            throw new StreamProcessException(
                sprintf('Error during callable execution: %s', $e->getMessage())
            );
        }
    }

    public function __invoke(): string
    {
        if ($this->isClosed === true) {
            throw new StreamProcessException('Stream is already closed');
        }

        if (rewind($this->fp) === false) {
            throw new StreamProcessException('Cannot rewind stream');
        };

        $data = '';
        while (true) {
            $chunk = fgets($this->fp);
            if ($chunk === false) {
                break;
            }
            $data .= $chunk;
        }

        if (fclose($this->fp) === false) {
            throw new StreamProcessException('Cannot close Stream');
        };

        $this->isClosed = true;

        return $data;
    }
}

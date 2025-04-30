<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Streams;

use Exception;

use function fclose;
use function fopen;
use function fread;
use function fwrite;
use function rewind;
use function sprintf;

class TempStream implements TempStreamInterface
{
    /** @var resource $fp */
    protected $fp;
    protected const PROTOCOL = 'php://temp';
    protected const MODE     = 'rw';
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

    /** @param mixed $args */
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
        } catch (Exception $e) {
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
        }

        $data  = '';
        $chank = fread($this->fp, 1024);
        if ($chank === false) {
            fclose($this->fp);
            throw new StreamProcessException(
                sprintf('Cannot read from stream')
            );
        }
        while ($chank !== '') {
            $data .= $chank;
            $chank = fread($this->fp, 1024);
            if ($chank === false) {
                fclose($this->fp);
                throw new StreamProcessException(
                    sprintf('Cannot close temp stream')
                );
            }
        }

        if (fclose($this->fp) === false) {
            throw new StreamProcessException('Cannot close Stream');
        }

        $this->isClosed = true;

        return $data;
    }
}

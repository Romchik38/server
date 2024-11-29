<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Services\Streams\StreamProcessException;
use Romchik38\Server\Services\Streams\TempStream;

class TempStreamTest extends TestCase
{
    public function testWrite(): void
    {
        $stream = new TempStream();
        $stream->write('hello');
        $data = $stream();
        $this->assertSame('hello', $data);
    }

    public function testWriteFromCallable(): void
    {
        $stream = new TempStream();
        $fn = function ($resource, string $data) {
            fwrite($resource, $data);
        };
        $stream->writeFromCallable($fn, 0, null, 'hello');
        $data = $stream();
        $this->assertSame('hello', $data);
    }

    public function testWriteFromCallableCallbackReturnFalse(): void
    {
        $stream = new TempStream();
        $fn = function ($resource, string $data) {
            return false;
        };
        $this->expectException(StreamProcessException::class);
        $stream->writeFromCallable($fn, 0, null, 'hello');
    }

    public function testWriteFromCallableCallbackThrowsException(): void
    {
        $stream = new TempStream();
        $fn = function ($resource, string $data) {
            throw new \RuntimeException('error');
        };
        $this->expectException(StreamProcessException::class);
        $stream->writeFromCallable($fn, 0, null, 'hello');
    }

    public function testInvokeDoubleCall(): void
    {
        $stream = new TempStream();
        $fn = function ($resource, string $data) {
            fwrite($resource, $data);
        };
        $stream->writeFromCallable($fn, 0, null, 'hello');
        $firstData = $stream();
        $this->expectException(StreamProcessException::class);
        $secondData = $stream();
    }
}

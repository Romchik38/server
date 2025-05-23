<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Files;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Utils\Files\FileLoader;
use Romchik38\Server\Utils\Files\FileLoaderException;

class FileLoaderTest extends TestCase
{
    public function testLoadDirWithoutSlash(): void
    {
        $fileLoader = new FileLoader(__DIR__);
        $data       = $fileLoader->load('test.txt');

        $this->assertSame('hello', $data);
    }

    public function testLoadDirWithSlash(): void
    {
        $fileLoader = new FileLoader(__DIR__ . '/');
        $data       = $fileLoader->load('test.txt');

        $this->assertSame('hello', $data);
    }

    public function testLoadFileHasLeadSlah(): void
    {
        $fileLoader = new FileLoader(__DIR__);
        $data       = $fileLoader->load('/test.txt');

        $this->assertSame('hello', $data);
    }

    public function testDirNotExist(): void
    {
        $this->expectException(FileLoaderException::class);
        $fileLoader = new FileLoader('/not-existing-dir');
        $data       = $fileLoader->load('test.txt');
    }

    public function testFileNotExist(): void
    {
        $this->expectException(FileLoaderException::class);
        $fileLoader = new FileLoader(__DIR__);
        $fileLoader->load('not-exist.txt');
    }
}

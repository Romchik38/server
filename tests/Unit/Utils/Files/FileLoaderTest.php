<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Files;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Utils\Files\FileLoader;
use Romchik38\Server\Utils\Files\FileLoaderException;

class FileLoaderTest extends TestCase
{
    public function testLoad(): void
    {
        $fileLoader = new FileLoader();
        $data       = $fileLoader->load(__DIR__ . '/test.txt');

        $this->assertSame('hello', $data);
    }

    public function testFileNotExist(): void
    {
        $this->expectException(FileLoaderException::class);
        $fileLoader = new FileLoader();
        $fileLoader->load(__DIR__ . '/not-exist.txt');
    }

    public function testFileIsDir(): void
    {
        $this->expectException(FileLoaderException::class);
        $fileLoader = new FileLoader();
        $fileLoader->load(__DIR__ . '/some_dir');
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Path;

final class PathTest extends TestCase
{
    public function testInvoke(): void
    {
        $parts = ['root', 'product'];
        $path  = new Path($parts);
        $this->assertSame($parts, $path());
    }

    public function testConstructThrowsErrorEmptyParts(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Path([]);
    }

    public function testFromEncodedUrlParts(): void
    {
        $parts = ['root', 'product%25'];
        $path  = Path::fromEncodedUrlParts($parts);
        $this->assertSame(['root', 'product%'], $path());
    }

    public function testFromEncodedUrlPartsThrowsErrorOnSpecailChars(): void
    {
        $parts = ['root', 'product '];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('path name part product  is invalid');
        Path::fromEncodedUrlParts($parts);
    }

    public function testFromEncodedUrlPartsThrowsErrorOnNonString(): void
    {
        $parts = [1, false];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('param path part is invalid');
        Path::fromEncodedUrlParts($parts);
    }
}

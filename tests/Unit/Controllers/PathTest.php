<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Controllers;

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
}

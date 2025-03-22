<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Controllers;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Controllers\Name;

final class NameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('');
    }

    public function testDigits(): void
    {
        $n1 = new Name('a1a');
        $this->assertSame('a1a', $n1());
    }

    public function testNotAscii(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('і');
    }

    public function testUpperCase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('Product');
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Name;

final class NameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('');
    }

    public function testDigitsChars(): void
    {
        $n1 = new Name('a1a');
        $this->assertSame('a1a', $n1());
    }

    public function testDigits(): void
    {
        $n1 = new Name('0123');
        $this->assertSame('0123', $n1());
    }

    public function testNotAscii(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Name('і');
    }

    public function testUpperCase(): void
    {
        $n1 = new Name('Product');
        $this->assertSame('Product', $n1());
    }

    public function testDash(): void
    {
        $n1 = new Name('a-1a');
        $this->assertSame('a-1a', $n1());
    }

    public function testExclamationMark(): void
    {
        $n1 = new Name('!');
        $this->assertSame('!', $n1());
    }

    public function testDollar(): void
    {
        $n1 = new Name('$');
        $this->assertSame('$', $n1());
    }

    public function testUnderscore(): void
    {
        $n1 = new Name('_');
        $this->assertSame('_', $n1());
    }

    public function testDotComma(): void
    {
        $n1 = new Name('.,');
        $this->assertSame('.,', $n1());
    }

    public function testPlusStarSingleQuote(): void
    {
        $n1 = new Name('+*\'');
        $this->assertSame('+*\'', $n1());
    }

    public function testParentheses(): void
    {
        $n1 = new Name('()');
        $this->assertSame('()', $n1());
    }

    public function testPercent(): void
    {
        $n1 = new Name('as%AC');
        $this->assertSame('as%AC', $n1());
    }
}

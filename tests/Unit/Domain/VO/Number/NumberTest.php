<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Domain\VO\Number;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Domain\VO\Number\Number;

final class NumberTest extends TestCase
{
    public function testInvoke(): void
    {
        $value  = 1;
        $number = new Number($value);

        $this->assertSame($value, ($number)());
    }

    public function testToString(): void
    {
        $value  = 1;
        $number = new Number($value);

        $this->assertSame('1', (string) $number);
    }

    public function testFromString(): void
    {
        $value  = '1';
        $number = Number::fromString($value);

        $this->assertSame(1, ($number)());
    }

    public function testFromStringNegative(): void
    {
        $value  = '-1';
        $number = Number::fromString($value);

        $this->assertSame(-1, ($number)());
    }

    public function testFromStringThrowsError(): void
    {
        $value = '1.0';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('param number 1.0 is invalid');
        Number::fromString($value);
    }
}

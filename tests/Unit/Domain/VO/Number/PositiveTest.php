<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Domain\VO\Number;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Domain\VO\Number\Positive;

final class PositiveTest extends TestCase
{
    public function testConstruct(): void
    {
        $value  = 1;
        $number = new Positive($value);

        $this->assertSame($value, ($number)());
    }

    public function testConstructThrowsErrorOnNegative(): void
    {
        $value = -10;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('param positive-number must be greater than 0');

        new Positive($value);
    }

    public function testConstructThrowsErrorOnZerro(): void
    {
        $value = 0;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('param positive-number must be greater than 0');

        new Positive($value);
    }

    public function testName(): void
    {
        $p = new Positive(1);
        $this->assertSame(Positive::NAME, $p->getName());
    }

    public function testInstance(): void
    {
        $p = Positive::fromString('1');

        $this->assertSame(true, $p instanceof Positive);
    }
}

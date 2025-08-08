<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Domain\VO\Number;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Domain\VO\Number\NonNegative;

use function sprintf;

final class NonNegativeTest extends TestCase
{
    public function testConstructWithPositive(): void
    {
        $number = 1;
        $vo     = new NonNegative($number);
        $this->assertSame($number, $vo());
    }

    public function testConstructWithZero(): void
    {
        $number = 0;
        $vo     = new NonNegative($number);
        $this->assertSame($number, $vo());
    }

    public function testConstructWhrowsErrorOnNegative(): void
    {
        $number = -1;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('param %s must be greater or equal than 0', NonNegative::NAME));

        new NonNegative($number);
    }
}

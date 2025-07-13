<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Domain\VO\Text;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Domain\VO\Text\NonEmpty;

final class NonEmptyTest extends TestCase
{
    public function testConstruct(): void
    {
        $value = 'some text';
        $t     = new NonEmpty($value);

        $this->assertSame($value, ($t)());
    }

    public function testConstructThrowsErrorOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('param non empty text is empty');
        new NonEmpty('');
    }
}

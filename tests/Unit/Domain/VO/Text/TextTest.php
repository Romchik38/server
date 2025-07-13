<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Domain\VO\Text;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Domain\VO\Text\Text;

final class TextTest extends TestCase
{
    /** also tested __invoke method
     */
    public function testConstruct(): void
    {
        $value = 'some text';
        $t     = new Text($value);

        $this->assertSame($value, ($t)());
    }

    public function testToString(): void
    {
        $value = 'some text';
        $t     = new Text($value);

        $this->assertSame($value, (string) $t);
    }
}

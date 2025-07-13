<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Domain\VO;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Domain\VO\AbstractVo;

final class VoTest extends TestCase
{
    public function testGetName(): void
    {
        $v = $this->createVo('');

        $this->assertSame(AbstractVo::NAME, $v->getName());
    }

    public function testToString(): void
    {
        $value = 'some value';
        $v     = $this->createVo($value);

        $this->assertSame($value, (string) $v);
    }

    private function createVo(string $value): AbstractVo
    {
        return new class ($value) extends AbstractVo {
            public function __construct(
                private readonly string $value
            ) {
            }

            public function __toString(): string
            {
                return $this->value;
            }
        };
    }
}

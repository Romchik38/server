<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Domain\VO;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Domain\VO\Vo;

final class VoTest extends TestCase
{
    public function testGetName(): void
    {
        $v = new Vo();
        $this->assertSame(Vo::NAME, $v->getName());
    }
}

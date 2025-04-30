<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Utils\DynamicRoot;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Utils\DynamicRoot\DynamicRootDTO;

class DynamicRootDTOTest extends TestCase
{
    public function testGetName()
    {
        $name = 'en';
        $dto  = new DynamicRootDTO($name);

        $this->assertSame($name, $dto->getName());
    }
}

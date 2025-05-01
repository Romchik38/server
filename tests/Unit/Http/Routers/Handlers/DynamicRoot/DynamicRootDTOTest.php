<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Handlers\DynamicRoot;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootDTO;

class DynamicRootDTOTest extends TestCase
{
    public function testGetName()
    {
        $name = 'en';
        $dto  = new DynamicRootDTO($name);

        $this->assertSame($name, $dto->getName());
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\DTO\DynamicRoot;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Services\DynamicRoot\DynamicRootDTO;

class DynamicRootDTOTest extends TestCase
{
    public function testGetName()
    {
        $name = 'en';
        $dto  = new DynamicRootDTO($name);

        $this->assertSame($name, $dto->getName());
    }
}

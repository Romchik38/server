<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTO;

class DynamicRootDTOTest extends TestCase
{
    public function testGetName()
    {
        $name = 'en';
        $dto = new DynamicRootDTO($name);

        $this->assertSame($name, $dto->getName());
    }
}

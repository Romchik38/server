<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DymanicRoot\DymanicRootDTO;

class DymanicRootDTOTest extends TestCase
{
    public function testGetName()
    {
        $name = 'en';
        $dto = new DymanicRootDTO($name);

        $this->assertSame($name, $dto->getName());
    }
}

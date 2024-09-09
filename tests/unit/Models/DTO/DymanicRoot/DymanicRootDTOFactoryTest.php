<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DymanicRoot\DymanicRootDTOFactory;

class DymanicRootDTOFactoryTest extends TestCase {
    public function testCreate(){
        $rootName = 'en';
        $factory = new DymanicRootDTOFactory();
        $dto = $factory->create($rootName);

        $this->assertSame($rootName, $dto->getName());
    }
}
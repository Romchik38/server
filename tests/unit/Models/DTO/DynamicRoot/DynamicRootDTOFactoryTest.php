<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTOFactory;

class DynamicRootDTOFactoryTest extends TestCase {
    public function testCreate(){
        $rootName = 'en';
        $factory = new DynamicRootDTOFactory();
        $dto = $factory->create($rootName);

        $this->assertSame($rootName, $dto->getName());
    }
}
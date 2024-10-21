<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Http\LinkTree\LinkTreeDTO;
use Romchik38\Server\Models\DTO\Http\LinkTree\LinkTreeDTOFactory;

class LinkTreeDTOFactoryTest extends TestCase
{
    public function testCreate()
    {
        $name = 'home';
        $description = 'home page';
        $url = '/';

        $childName = 'about';
        $childDescription = 'about page';
        $childUrl = '/about';

        $factory = new LinkTreeDTOFactory();

        $child = $factory->create($childName, $childDescription, $childUrl, []);
        $dto = $factory->create($name, $description, $url, [$child]);

        $this->assertSame($name, $dto->getName());
        $this->assertSame($description, $dto->getDescription());
        $this->assertSame($url, $dto->getUrl());
        $this->assertSame([$child], $dto->getChildren());
    }
}

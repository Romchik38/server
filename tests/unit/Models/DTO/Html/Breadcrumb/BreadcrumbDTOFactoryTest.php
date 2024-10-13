<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Html\Breadcrumb\BreadcrumbDTOFactory;

class BreadcrumbDTOFactoryTest extends TestCase
{
    public function testCreate()
    {
        $name = 'Home Page';
        $description = 'Home description';
        $url = '/';

        $factory = new BreadcrumbDTOFactory();

        $dto = $factory->create($name, $description, $url, null);

        $this->assertSame($name, $dto->getName());
        $this->assertSame($description, $dto->getDescription());
        $this->assertSame($url, $dto->getUrl());
        $this->assertSame(null, $dto->getPrev());
    }
}

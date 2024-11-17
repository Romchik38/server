<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Http\LinkTree\LinkTreeDTO;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

class LinkTreeDTOTest extends TestCase
{
    public function testGetChildren()
    {
        $child = new LinkTreeDTO('about', 'about page', '/about', []);
        $dto = new LinkTreeDTO('home', 'home page', '/', [$child]);

        $children = $dto->getChildren();

        $this->assertSame([$child], $children);
    }

    public function testConstructThrowsExceptionEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $dto = new LinkTreeDTO('', 'home page', '/', []);
    }

    public function testConstructThrowsExceptionEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $dto = new LinkTreeDTO('home', '', '/', []);
    }

    public function testConstructThrowsExceptionEmptyUrl(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $dto = new LinkTreeDTO('home', 'Home page', '', []);
    }

    public function testCreate()
    {
        $name = 'home';
        $description = 'home page';
        $url = '/';

        $childName = 'about';
        $childDescription = 'about page';
        $childUrl = '/about';

        $child = new LinkTreeDTO($childName, $childDescription, $childUrl, []);
        $dto = new LinkTreeDTO($name, $description, $url, [$child]);

        $this->assertSame($name, $dto->getName());
        $this->assertSame($description, $dto->getDescription());
        $this->assertSame($url, $dto->getUrl());
        $this->assertSame([$child], $dto->getChildren());
    }
}

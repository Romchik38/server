<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\DTO\Http\LinkTree\LinkTreeDTO;

class LinkTreeDTOTest extends TestCase
{
    public function testGetChildren()
    {
        $child = new LinkTreeDTO('about', 'about page', '/about', []);
        $dto = new LinkTreeDTO('home', 'home page', '/', [$child]);

        $children = $dto->getChildren();

        $this->assertSame([$child], $children);
    }
}

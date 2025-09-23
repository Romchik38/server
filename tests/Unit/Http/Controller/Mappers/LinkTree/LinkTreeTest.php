<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\LinkTree;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Dto\ControllerDTO;
use Romchik38\Server\Http\Controller\Dto\ControllerDTOInterface;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbInterface;
use Romchik38\Server\Http\Controller\Mappers\Link\LinkDTO;
use Romchik38\Server\Http\Controller\Mappers\LinkTree\LinkTree;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootDTOInterface;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootInterface;

use function count;

final class LinkTreeTest extends TestCase
{
    public function testGetLinkTreeDTOwithDynamicRootandWithCollection(): void
    {
        $dynamicRoot       = $this->createMock(DynamicRootInterface::class);
        $dynamicRootDto    = $this->createMock(DynamicRootDTOInterface::class);
        $language          = 'en';
        $rootControllerDto = $this->createRootControllerDTO();

        $dynamicRoot->expects($this->once())->method('getCurrentRoot')
            ->willReturn($dynamicRootDto);

        $dynamicRootDto->expects($this->once())->method('getName')
            ->willReturn($language);

        $linkTreeService = new LinkTree($dynamicRoot);

        $dto = $linkTreeService->getLinkTreeDTO($rootControllerDto);

        $this->assertSame('home', $dto->getName());
        $this->assertSame('Home page', $dto->getDescription());
        $this->assertSame('/en', $dto->getUrl());

        $children = $dto->getChildren();
        $this->assertSame(2, count($children));

        [$child1, $child2] = $children;

        $this->assertSame('about', $child1->getName());
        $this->assertSame('About page', $child1->getDescription());
        $this->assertSame('/en/about', $child1->getUrl());

        $this->assertSame('sitemap', $child2->getName());
        $this->assertSame('Sitemap page', $child2->getDescription());
        $this->assertSame('/en/sitemap', $child2->getUrl());
    }

    public function testGetLinkTreeDTOWithoutDynamicRoot(): void
    {
        $rootControllerDto = $this->createRootControllerDTO();

        $linkTreeService = new LinkTree();
        $dto             = $linkTreeService->getLinkTreeDTO($rootControllerDto);

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $dto->getName());
        $this->assertSame('Home page', $dto->getDescription());
        $this->assertSame('/', $dto->getUrl());

        $children = $dto->getChildren();
        $this->assertSame(2, count($children));

        [$child1, $child2] = $children;

        $this->assertSame('about', $child1->getName());
        $this->assertSame('About page', $child1->getDescription());
        $this->assertSame('/about', $child1->getUrl());

        $this->assertSame('sitemap', $child2->getName());
        $this->assertSame('Sitemap page', $child2->getDescription());
        $this->assertSame('/sitemap', $child2->getUrl());
    }

    public function testGetLinkTreeDTOWithoutDynamicRootWithSpecialChars(): void
    {
        $child1            = new ControllerDTO('news 2025', ['root'], [], 'News 2025 page');
        $rootControllerDto = new ControllerDTO('root', [], [$child1], 'Home page');

        $linkTreeService = new LinkTree();
        $dto             = $linkTreeService->getLinkTreeDTO($rootControllerDto);
        $children        = $dto->getChildren();
        $child1          = $children[0];

        $this->assertSame('news 2025', $child1->getName());
        $this->assertSame('/news+2025', $child1->getUrl());
    }

    protected function createRootControllerDTO(): ControllerDTOInterface
    {
        $child1 = new ControllerDTO('about', ['root'], [], 'About page');
        $child2 = new ControllerDTO('sitemap', ['root'], [], 'Sitemap page');
        return new ControllerDTO('root', [], [$child1, $child2], 'Home page');
    }

    protected function createLinkDTOs(): array
    {
        return [
            new LinkDTO('Home', 'Home Page', '/en'),
            new LinkDTO('About', 'About Page', '/en/about'),
        ];
    }
}

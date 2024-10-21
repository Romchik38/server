<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Models\DTO\Http\LinkTree\LinkTreeDTOFactory;
use Romchik38\Server\Services\Mappers\LinkTree\Http\LinkTree;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;
use Romchik38\Server\Api\Models\DTO\DynamicRoot\DynamicRootDTOInterface;
use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOCollectionInterface;
use Romchik38\Server\Models\DTO\Http\Link\LinkDTO;

class LinkTreeTest extends TestCase
{
    protected LinkTreeDTOFactory $linkTreeDTOFactory;
    protected $dynamicRoot;
    protected $dynamicRootDTO;
    protected $linkDTOCollection;


    public function setUp(): void
    {
        $this->linkTreeDTOFactory = new LinkTreeDTOFactory();
        $this->dynamicRoot = $this->createMock(DynamicRootInterface::class);
        $this->dynamicRootDTO = $this->createMock(DynamicRootDTOInterface::class);
        $this->linkDTOCollection = $this->createMock(LinkDTOCollectionInterface::class);
    }

    public function testGetLinkTreeDTOwithDynamicRootandWithCollection(): void
    {
        $language = 'en';
        $rootControllerDTO = $this->createRootControllerDTO();

        $this->dynamicRoot->expects($this->once())->method('getCurrentRoot')
            ->willReturn($this->dynamicRootDTO);

        $this->dynamicRootDTO->expects($this->once())->method('getName')
            ->willReturn($language);

        $this->linkDTOCollection->expects($this->once())->method('getLinksByPaths')
            ->willReturn($this->createLinkDTOs());

        $linkTreeService = new LinkTree(
            $this->linkTreeDTOFactory,
            $this->linkDTOCollection,
            $this->dynamicRoot
        );

        $dto = $linkTreeService->getLinkTreeDTO($rootControllerDTO);

        $this->assertSame('Home', $dto->getName());
        $this->assertSame('Home Page', $dto->getDescription());
        $this->assertSame('/en', $dto->getUrl());

        $children = $dto->getChildren();
        $this->assertSame(2, count($children));

        [$child1, $child2] = $children;

        $this->assertSame('About', $child1->getName());
        $this->assertSame('About Page', $child1->getDescription());
        $this->assertSame('/en/about', $child1->getUrl());

        $this->assertSame('sitemap', $child2->getName());
        $this->assertSame('sitemap', $child2->getDescription());
        $this->assertSame('/en/sitemap', $child2->getUrl());
    }

    public function testGetLinkTreeDTOwithDynamicRootandWithoutCollection(): void
    {
        $language = 'en';
        $rootControllerDTO = $this->createRootControllerDTO();

        $this->dynamicRoot->expects($this->once())->method('getCurrentRoot')
            ->willReturn($this->dynamicRootDTO);

        $this->dynamicRootDTO->expects($this->once())->method('getName')
            ->willReturn($language);

        $linkTreeService = new LinkTree(
            $this->linkTreeDTOFactory,
            null,
            $this->dynamicRoot
        );

        $dto = $linkTreeService->getLinkTreeDTO($rootControllerDTO);

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $dto->getName());
        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $dto->getDescription());
        $this->assertSame('/en', $dto->getUrl());

        $children = $dto->getChildren();
        $this->assertSame(2, count($children));

        [$child1, $child2] = $children;

        $this->assertSame('about', $child1->getName());
        $this->assertSame('about', $child1->getDescription());
        $this->assertSame('/en/about', $child1->getUrl());

        $this->assertSame('sitemap', $child2->getName());
        $this->assertSame('sitemap', $child2->getDescription());
        $this->assertSame('/en/sitemap', $child2->getUrl());
    }

    public function testGetLinkTreeDTOWithoutDynamicRootandWithoutCollection(): void
    {
        $rootControllerDTO = $this->createRootControllerDTO();

        $linkTreeService = new LinkTree($this->linkTreeDTOFactory);
        $dto = $linkTreeService->getLinkTreeDTO($rootControllerDTO);

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $dto->getName());
        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $dto->getDescription());
        $this->assertSame('/', $dto->getUrl());

        $children = $dto->getChildren();
        $this->assertSame(2, count($children));

        [$child1, $child2] = $children;

        $this->assertSame('about', $child1->getName());
        $this->assertSame('about', $child1->getDescription());
        $this->assertSame('/about', $child1->getUrl());

        $this->assertSame('sitemap', $child2->getName());
        $this->assertSame('sitemap', $child2->getDescription());
        $this->assertSame('/sitemap', $child2->getUrl());
    }


    protected function createRootControllerDTO(): ControllerDTOInterface
    {
        $child1 = new ControllerDTO('about', ['root'], []);
        $child2 = new ControllerDTO('sitemap', ['root'], []);
        $rootControllerDTO = new ControllerDTO('root', [], [$child1, $child2]);
        return $rootControllerDTO;
    }

    protected function createLinkDTOs(): array
    {
        return [
            new LinkDTO('Home', 'Home Page', '/en'),
            new LinkDTO('About', 'About Page', '/en/about')
        ];
    }
}

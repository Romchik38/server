<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Mappers\Breadcrumb\Http;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Services\DynamicRoot\DynamicRootDTO;
use Romchik38\Server\Services\Mappers\Breadcrumb\Http\Breadcrumb;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;

final class BreadcrumbTest extends TestCase
{
    protected string $rootControllerDtoName         = 'root';
    protected string $rootControllerDtoDescription  = 'Home';
    protected string $aboutControllerDtoName        = 'about';
    protected string $aboutControllerDtoDescription = 'About';

    /**
     * LinkDTOCollection returns 0 results
     * In this case the breadcrumb creates its DTOs with only controller and action names
     */
    public function testGetBreadcrumbDTOWithDynamicRoot(): void
    {
        $dynamicRootDto           = $this->createMock(DynamicRootDTO::class);
        $dynamicRootForBreadcrumb = $this->createMock(DynamicRoot::class);
        $controllerTree           = $this->createMock(ControllerTree::class);
        $controller               = $this->createMock(Controller::class);

        $action = 'about';

        $controllerDto = $this->createControllerDto();

        $language = 'en';

        $dynamicRootDto->method('getName')->willReturn($language);
        $dynamicRootForBreadcrumb->expects($this->once())->method('getCurrentRoot')
            ->willReturn($dynamicRootDto);

        $controllerTree->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($controller, $action)
            ->willReturn($controllerDto);

        $breadcrumb = new Breadcrumb($controllerTree, $dynamicRootForBreadcrumb);

        $breadcrumbDtoAbout = $breadcrumb->getBreadcrumbDTO($controller, $action);
        $breadcrumbDtoRoot  = $breadcrumbDtoAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDtoRoot->getName());
        $this->assertSame('Home', $breadcrumbDtoRoot->getDescription());
        $this->assertSame('/en', $breadcrumbDtoRoot->getUrl());

        $this->assertSame($this->aboutControllerDtoName, $breadcrumbDtoAbout->getName());
        $this->assertSame('About', $breadcrumbDtoAbout->getDescription());
        $this->assertSame('/en/about', $breadcrumbDtoAbout->getUrl());
    }

    public function testGetBreadcrumbDTOWithoutDynamicRoot()
    {
        $action         = 'about';
        $controllerTree = $this->createMock(ControllerTree::class);
        $controller     = $this->createMock(Controller::class);
        $controllerDto  = $this->createControllerDto();

        $controllerTree->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($controller, $action)
            ->willReturn($controllerDto);

        $breadcrumb = new Breadcrumb($controllerTree);

        $breadcrumbDtoAbout = $breadcrumb->getBreadcrumbDTO($controller, $action);
        $breadcrumbDtoRoot  = $breadcrumbDtoAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDtoRoot->getName());
        $this->assertSame('Home', $breadcrumbDtoRoot->getDescription());
        $this->assertSame('/', $breadcrumbDtoRoot->getUrl());

        $this->assertSame($this->aboutControllerDtoName, $breadcrumbDtoAbout->getName());
        $this->assertSame('About', $breadcrumbDtoAbout->getDescription());
        $this->assertSame('/about', $breadcrumbDtoAbout->getUrl());
    }

    /**
     * Creates 2 controllerDTOs
     *  - root
     *  - about
     *
     * @return ControllerDTOInterface ControllerDTO with root in a front
     */
    protected function createControllerDTO(): ControllerDTOInterface
    {
        $about = new ControllerDTO(
            $this->aboutControllerDtoName,
            [$this->rootControllerDtoName],
            [],
            $this->aboutControllerDtoDescription
        );

        return new ControllerDTO(
            $this->rootControllerDtoName,
            [],
            [$about],
            $this->rootControllerDtoDescription
        );
    }
}

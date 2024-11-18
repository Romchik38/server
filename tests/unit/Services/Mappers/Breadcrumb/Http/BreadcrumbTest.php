<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Services\Mappers\Breadcrumb\Http\Breadcrumb;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTO;

class BreadcrumbTest extends TestCase
{
    protected $controllerTree;
    protected $dynamicRootForBreadcrumb;
    protected $controller;
    protected $dynamicRootDTO;
    protected string $rootControllerDTOName = 'root';
    protected string $rootControllerDTODescription = 'Home';
    protected string $aboutControllerDTOName = 'about';
    protected string $aboutControllerDTODescription = 'About';


    public function setUp(): void
    {
        $this->controllerTree = $this->createMock(ControllerTree::class);
        $this->dynamicRootForBreadcrumb = $this->createMock(DynamicRoot::class);
        $this->controller = $this->createMock(Controller::class);
        $this->dynamicRootDTO = $this->createMock(DynamicRootDTO::class);
    }

    /**
     * LinkDTOCollection returns 0 results
     * In this case the breadcrumb creates its DTOs with only controller and action names
     */
    public function testGetBreadcrumbDTOWithDynamicRoot(): void
    {
        $action = 'about';

        $controllerDTO = $this->createControllerDTO();

        $language = 'en';

        $emptyString = '';

        $this->dynamicRootDTO->method('getName')->willReturn($language);
        $this->dynamicRootForBreadcrumb->expects($this->once())->method('getCurrentRoot')
            ->willReturn($this->dynamicRootDTO);

        $this->controllerTree->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $breadcrumb = new Breadcrumb($this->controllerTree, $this->dynamicRootForBreadcrumb);

        $breadcrumbDTOAbout = $breadcrumb->getBreadcrumbDTO($this->controller, $action);
        $breadcrumbDTORoot = $breadcrumbDTOAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDTORoot->getName());
        $this->assertSame('Home', $breadcrumbDTORoot->getDescription());
        $this->assertSame('/en', $breadcrumbDTORoot->getUrl());

        $this->assertSame($this->aboutControllerDTOName, $breadcrumbDTOAbout->getName());
        $this->assertSame('About', $breadcrumbDTOAbout->getDescription());
        $this->assertSame('/en/about', $breadcrumbDTOAbout->getUrl());
    }

    public function testGetBreadcrumbDTOWithoutDynamicRoot()
    {
        $action = 'about';
        $emptyString = '';

        $controllerDTO = $this->createControllerDTO();

        $this->controllerTree->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $breadcrumb = new Breadcrumb($this->controllerTree);


        $breadcrumbDTOAbout = $breadcrumb->getBreadcrumbDTO($this->controller, $action);
        $breadcrumbDTORoot = $breadcrumbDTOAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDTORoot->getName());
        $this->assertSame('Home', $breadcrumbDTORoot->getDescription());
        $this->assertSame('/', $breadcrumbDTORoot->getUrl());

        $this->assertSame($this->aboutControllerDTOName, $breadcrumbDTOAbout->getName());
        $this->assertSame('About', $breadcrumbDTOAbout->getDescription());
        $this->assertSame('/about', $breadcrumbDTOAbout->getUrl());
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
            $this->aboutControllerDTOName,
            [$this->rootControllerDTOName],
            [],
            $this->aboutControllerDTODescription
        );

        return new ControllerDTO(
            $this->rootControllerDTOName,
            [],
            [$about],
            $this->rootControllerDTODescription
        );
    }
}

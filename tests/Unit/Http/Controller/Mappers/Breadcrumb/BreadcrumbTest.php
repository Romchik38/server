<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Mappers\Breadcrumb;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\Breadcrumb;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbInterface;
use Romchik38\Server\Http\Controller\Mappers\ControllerTree\ControllerTree;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRoot;

final class BreadcrumbTest extends TestCase
{
    /**
     * LinkDTOCollection returns 0 results
     * In this case the breadcrumb creates its DTOs with only controller and action names
     */
    public function testGetBreadcrumbDTOWithDynamicRoot(): void
    {
        $language = 'en';

        $dynanicRoot = new DynamicRoot('en', ['en', 'uk']);
        $dynanicRoot->setCurrentRoot($language);
        $controllerTree  = new ControllerTree();
        $breadcrumb      = new Breadcrumb($controllerTree, $dynanicRoot);
        $controllerRoot  = new Controller('root');
        $controllerAbout = new Controller('about');
        $controllerRoot->setChild($controllerAbout);
        $controllerAbout->setCurrentParent($controllerRoot);

        $breadcrumbDtoAbout = $breadcrumb->getBreadcrumbDTO($controllerAbout, '');
        $breadcrumbDtoRoot  = $breadcrumbDtoAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDtoRoot->getName());
        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDtoRoot->getDescription());
        $this->assertSame('/en', $breadcrumbDtoRoot->getUrl());

        $this->assertSame('about', $breadcrumbDtoAbout->getName());
        $this->assertSame('about', $breadcrumbDtoAbout->getDescription());
        $this->assertSame('/en/about', $breadcrumbDtoAbout->getUrl());
    }

    public function testGetBreadcrumbDTOWithoutDynamicRoot()
    {
        $controllerTree  = new ControllerTree();
        $controllerRoot  = new Controller('root');
        $controllerAbout = new Controller('about');
        $controllerRoot->setChild($controllerAbout);
        $controllerAbout->setCurrentParent($controllerRoot);
        $breadcrumb = new Breadcrumb($controllerTree);

        $breadcrumbDtoAbout = $breadcrumb->getBreadcrumbDTO($controllerAbout, '');
        $breadcrumbDtoRoot  = $breadcrumbDtoAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDtoRoot->getName());
        $this->assertSame('home', $breadcrumbDtoRoot->getDescription());
        $this->assertSame('/', $breadcrumbDtoRoot->getUrl());

        $this->assertSame('about', $breadcrumbDtoAbout->getName());
        $this->assertSame('about', $breadcrumbDtoAbout->getDescription());
        $this->assertSame('/about', $breadcrumbDtoAbout->getUrl());
    }

    public function testGetBreadcrumbDTOWithSpecialChars()
    {
        $controllerTree  = new ControllerTree();
        $controllerRoot  = new Controller('root');
        $controllerAbout = new Controller('about company');
        $controllerRoot->setChild($controllerAbout);
        $controllerAbout->setCurrentParent($controllerRoot);
        $breadcrumb = new Breadcrumb($controllerTree);

        $breadcrumbDtoAbout = $breadcrumb->getBreadcrumbDTO($controllerAbout, '');

        $this->assertSame('about company', $breadcrumbDtoAbout->getName());
        $this->assertSame('/about+company', $breadcrumbDtoAbout->getUrl());
    }
}

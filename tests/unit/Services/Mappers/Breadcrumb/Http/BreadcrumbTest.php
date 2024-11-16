<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOCollectionInterface;
use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOFactoryInterface;
use Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Services\Mappers\Breadcrumb\Http\Breadcrumb;
use Romchik38\Server\Services\Mappers\ControllerTree\ControllerTree;
use Romchik38\Server\Models\DTO\Http\Breadcrumb\BreadcrumbDTOFactory;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Models\DTO\Http\Link\LinkDTOFactory;
use Romchik38\Server\Models\Sql\DatabasePostgresql;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTO;

class BreadcrumbTest extends TestCase
{

    protected $dynamicRootForCollection;
    protected $database;
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
        $this->dynamicRootForCollection = $this->createMock(DynamicRoot::class);
        $this->controllerTree = $this->createMock(ControllerTree::class);
        $this->dynamicRootForBreadcrumb = $this->createMock(DynamicRoot::class);
        $this->database = $this->createMock(DatabasePostgresql::class);
        $this->controller = $this->createMock(Controller::class);
        $this->dynamicRootDTO = $this->createMock(DynamicRootDTO::class);
    }

    /**
     * LinkDTOCollection returns 2 results
     * In this case the breadcrumb uses the results to create its DTOs
     */
    public function testGetBreadcrumbDTOWithDynamicRootAndRepositoryModels(): void
    {
        $action = 'about';

        $controllerDTO = $this->createControllerDTO();

        $language = 'en';

        $name1 = 'Home';
        $description1 = 'Home page';
        $linkId1 = '1';

        $name2 = 'About';
        $description2 = 'About page';
        $linkId2 = '2';

        $model1 = [
            'path' => ['root'],
            'link_id' => $linkId1,
            'language' => $language,
            'name' => $name1,
            'description' => $description1
        ];

        $model2 = [
            'path' => ['root', 'about'],
            'link_id' => $linkId2,
            'language' => $language,
            'name' => $name2,
            'description' => $description2
        ];

        $this->dynamicRootDTO->method('getName')->willReturn($language);
        $this->dynamicRootForBreadcrumb->expects($this->once())->method('getCurrentRoot')
            ->willReturn($this->dynamicRootDTO);

        $this->controllerTree->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $linkDTOcollection = $this->createLinkDTOCollection([$model1, $model2]);

        $breadcrumb = new Breadcrumb(
            $this->controllerTree,
            new BreadcrumbDTOFactory,
            $linkDTOcollection,
            $this->dynamicRootForBreadcrumb
        );

        $breadcrumbDTOAbout = $breadcrumb->getBreadcrumbDTO($this->controller, $action);
        $breadcrumbDTORoot = $breadcrumbDTOAbout->getPrev();

        $this->assertSame($name1, $breadcrumbDTORoot->getName());
        $this->assertSame($description1, $breadcrumbDTORoot->getDescription());
        $this->assertSame('/en', $breadcrumbDTORoot->getUrl());

        $this->assertSame($name2, $breadcrumbDTOAbout->getName());
        $this->assertSame($description2, $breadcrumbDTOAbout->getDescription());
        $this->assertSame('/en/about', $breadcrumbDTOAbout->getUrl());
    }

    /**
     * LinkDTOCollection returns 0 results
     * In this case the breadcrumb creates its DTOs with only controller and action names
     */
    public function testGetBreadcrumbDTOWithDynamicRootWithoutRepositoryModels(): void
    {
        $action = 'about';

        $controllerDTO = $this->createControllerDTO();

        $language = 'en';

        $emptyString = '';

        $this->dynamicRootDTO->method('getName')->willReturn($language);
        $this->dynamicRootForBreadcrumb->expects($this->once())->method('getCurrentRoot')
            ->willReturn($this->dynamicRootDTO);

        $linkDTOcollection = $this->createLinkDTOCollection([]);

        $this->controllerTree->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $breadcrumb = new Breadcrumb(
            $this->controllerTree,
            new BreadcrumbDTOFactory,
            $linkDTOcollection,
            $this->dynamicRootForBreadcrumb
        );

        $breadcrumbDTOAbout = $breadcrumb->getBreadcrumbDTO($this->controller, $action);
        $breadcrumbDTORoot = $breadcrumbDTOAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDTORoot->getName());
        $this->assertSame($emptyString, $breadcrumbDTORoot->getDescription());
        $this->assertSame('/en', $breadcrumbDTORoot->getUrl());

        $this->assertSame($this->aboutControllerDTOName, $breadcrumbDTOAbout->getName());
        $this->assertSame($emptyString, $breadcrumbDTOAbout->getDescription());
        $this->assertSame('/en/about', $breadcrumbDTOAbout->getUrl());
    }

    public function testGetBreadcrumbDTOWithoutDynamicRoot()
    {
        $action = 'about';
        $emptyString = '';

        $linkDTOcollection = $this->createLinkDTOCollectionDoNotUseDynamicRoot();

        $controllerDTO = $this->createControllerDTO();

        $this->controllerTree->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $breadcrumb = new Breadcrumb(
            $this->controllerTree,
            new BreadcrumbDTOFactory,
            $linkDTOcollection
        );


        $breadcrumbDTOAbout = $breadcrumb->getBreadcrumbDTO($this->controller, $action);
        $breadcrumbDTORoot = $breadcrumbDTOAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDTORoot->getName());
        $this->assertSame($emptyString, $breadcrumbDTORoot->getDescription());
        $this->assertSame('/', $breadcrumbDTORoot->getUrl());

        $this->assertSame($this->aboutControllerDTOName, $breadcrumbDTOAbout->getName());
        $this->assertSame($emptyString, $breadcrumbDTOAbout->getDescription());
        $this->assertSame('/about', $breadcrumbDTOAbout->getUrl());
    }

    /**
     * Creates LinkDTOCollection
     * 
     * @return LinkDTOCollectionInterface LinkDTOCollection
     */

    protected function createLinkDTOCollection(array $models)
    {
        return new class($models, new LinkDTOFactory()) implements LinkDTOCollectionInterface {
            protected array $hash = [];
            public function __construct(array $models, LinkDTOFactoryInterface $linkDTOFactory)
            {
                foreach ($models as $model) {
                    $path = $model['path'];
                    $key = serialize($path);
                    $newPath = $path;
                    $newPath[0] = $model['language'];
                    $url = '/' . implode('/', $newPath);
                    $linkDTO = $linkDTOFactory->create(
                        $model['name'],
                        $model['description'],
                        $url
                    );
                    $this->hash[$key] = $linkDTO;
                }
            }
            public function getLinksByPaths(array $paths = []): array
            {
                $result = [];
                foreach ($paths as $path) {
                    $dto = $this->hash[serialize($path)] ?? null;
                    if ($dto !== null) {
                        $result[] = $dto;
                    }
                }
                return $result;
            }
        };
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

    protected function createLinkDTOCollectionDoNotUseDynamicRoot(): LinkDTOCollectionInterface
    {
        return new class() implements LinkDTOCollectionInterface {
            public function getLinksByPaths(array $paths = []): array
            {
                return [];
            }
        };
    }
}

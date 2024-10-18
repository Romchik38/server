<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Models\DTO\Html\Link\LinkDTOCollectionInterface;
use Romchik38\Server\Api\Models\DTO\Html\Link\LinkDTOFactoryInterface;
use Romchik38\Server\Api\Services\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Services\Breadcrumb\Http\Breadcrumb;
use Romchik38\Server\Services\Sitemap\Sitemap;
use Romchik38\Server\Models\DTO\Html\Breadcrumb\BreadcrumbDTOFactory;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Models\DTO\Html\Link\LinkDTOFactory;
use Romchik38\Server\Models\Sql\DatabasePostgresql;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTO;

class BreadcrumbTest extends TestCase
{

    protected $dynamicRootForCollection;
    protected $database;
    protected $sitemap;
    protected $dynamicRootForBreadcrumb;
    protected $controller;
    protected $dynamicRootDTO;
    protected string $rootControllerName = 'root';
    protected string $aboutControllerName = 'about';

    public function setUp(): void
    {
        $this->dynamicRootForCollection = $this->createMock(DynamicRoot::class);
        $this->sitemap = $this->createMock(Sitemap::class);
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

        $this->sitemap->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $linkDTOcollection = $this->createLinkDTOCollection([$model1, $model2]);

        $breadcrumb = new Breadcrumb(
            $this->sitemap,
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

        $this->sitemap->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $breadcrumb = new Breadcrumb(
            $this->sitemap,
            new BreadcrumbDTOFactory,
            $linkDTOcollection,
            $this->dynamicRootForBreadcrumb
        );

        $breadcrumbDTOAbout = $breadcrumb->getBreadcrumbDTO($this->controller, $action);
        $breadcrumbDTORoot = $breadcrumbDTOAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDTORoot->getName());
        $this->assertSame($emptyString, $breadcrumbDTORoot->getDescription());
        $this->assertSame('/en', $breadcrumbDTORoot->getUrl());

        $this->assertSame($this->aboutControllerName, $breadcrumbDTOAbout->getName());
        $this->assertSame($emptyString, $breadcrumbDTOAbout->getDescription());
        $this->assertSame('/en/about', $breadcrumbDTOAbout->getUrl());
    }

    public function testGetBreadcrumbDTOWithoutDynamicRoot()
    {
        $action = 'about';
        $emptyString = '';

        $linkDTOcollection = $this->createLinkDTOCollectionDoNotUseDynamicRoot();

        $controllerDTO = $this->createControllerDTO();

        $this->sitemap->expects($this->once())->method('getOnlyLineRootControllerDTO')
            ->with($this->controller, $action)
            ->willReturn($controllerDTO);

        $breadcrumb = new Breadcrumb(
            $this->sitemap,
            new BreadcrumbDTOFactory,
            $linkDTOcollection
        );


        $breadcrumbDTOAbout = $breadcrumb->getBreadcrumbDTO($this->controller, $action);
        $breadcrumbDTORoot = $breadcrumbDTOAbout->getPrev();

        $this->assertSame(BreadcrumbInterface::HOME_PLACEHOLDER, $breadcrumbDTORoot->getName());
        $this->assertSame($emptyString, $breadcrumbDTORoot->getDescription());
        $this->assertSame('/', $breadcrumbDTORoot->getUrl());

        $this->assertSame($this->aboutControllerName, $breadcrumbDTOAbout->getName());
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
                // $model = [
                //     'path' => ['root', 'about'],
                //     'link_id' => '1',
                //     'language' => 'en',
                //     'name' => 'Home',
                //     'description' => 'Home page'
                // ];

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
            public function getLinksByPaths(array $paths): array
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
            $this->aboutControllerName,
            [$this->rootControllerName],
            []
        );

        return new ControllerDTO(
            $this->rootControllerName,
            [],
            [$about]
        );
    }

    protected function createLinkDTOCollectionDoNotUseDynamicRoot(): LinkDTOCollectionInterface
    {
        return new class() implements LinkDTOCollectionInterface {
            public function getLinksByPaths(array $paths): array
            {
                return [];
            }
        };
    }
}

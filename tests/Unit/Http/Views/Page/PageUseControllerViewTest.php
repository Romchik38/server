<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTO;
use Romchik38\Server\Http\Views\Errors\ViewBuildException;
use Romchik38\Server\Http\Views\Page\PageUseControllerView;
use Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\WithMetadata\MetaDataService;

use function sprintf;

class PageUseControllerViewTest extends TestCase
{
    public function testWithoutMetadata()
    {
        $pageName           = 'Home';
        $pageDescription    = 'Home page';
        $controller         = new Controller('root');
        $controllerDto      = new DefaultViewDTO($pageName, $pageDescription);
        $controllerTemplate = require_once __DIR__ . '/PageUseControllerView/WithoutMetadata/controllerTemplate.php';
        $generateTemplate   = require_once __DIR__ . '/PageUseControllerView/WithoutMetadata/generateTemplate.php';
        $view               = new PageUseControllerView($generateTemplate, $controllerTemplate);

        $view->setController($controller)->setControllerData($controllerDto);
        $html = $view->toString();

        $expectedTemplate = '<body><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf($expectedTemplate, $pageName, $pageDescription);

        $this->assertSame($expectedHtml, $html);
    }

    public function testWithMetadata()
    {
        $pageName           = 'Home';
        $pageDescription    = 'Home page';
        $userName           = 'User_1';
        $controller         = new Controller('root');
        $controllerDto      = new DefaultViewDTO($pageName, $pageDescription);
        $controllerTemplate = require __DIR__ . '/PageUseControllerView/WithMetadata/controllerTemplate.php';
        $generateTemplate   = require __DIR__ . '/PageUseControllerView/WithMetadata/generateTemplate.php';
        require_once __DIR__ . '/PageUseControllerView/WithMetadata/MetaDataService.php';
        $metaDataService = new MetaDataService($userName);
        $view            = new PageUseControllerView($generateTemplate, $controllerTemplate, $metaDataService);

        $view->setController($controller)->setControllerData($controllerDto);
        $html = $view->toString();

        $expectedTemplate = '<body><p>Hello %s</p><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf($expectedTemplate, $userName, $pageName, $pageDescription);

        $this->assertSame($expectedHtml, $html);
    }

    public function testtoStringThrowsErrorNullController(): void
    {
        $pageName           = 'Home';
        $pageDescription    = 'Home page';
        $controllerDto      = new DefaultViewDTO($pageName, $pageDescription);
        $controllerTemplate = require __DIR__ . '/PageUseControllerView/WithoutMetadata/controllerTemplate.php';
        $generateTemplate   = require __DIR__ . '/PageUseControllerView/WithoutMetadata/generateTemplate.php';
        $view               = new PageUseControllerView($generateTemplate, $controllerTemplate);
        $view->setControllerData($controllerDto);

        $this->expectException(ViewBuildException::class);
        $view->toString();
    }

    public function testtoStringThrowsErrorNullControllerData(): void
    {
        $controllerTemplate = require __DIR__ . '/PageUseControllerView/WithoutMetadata/controllerTemplate.php';
        $generateTemplate   = require __DIR__ . '/PageUseControllerView/WithoutMetadata/generateTemplate.php';
        $view               = new PageUseControllerView($generateTemplate, $controllerTemplate);
        $controller         = new Controller('root');
        $view->setController($controller);

        $this->expectException(ViewBuildException::class);
        $view->toString();
    }
}

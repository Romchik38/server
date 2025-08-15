<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTO;
use Romchik38\Server\Http\Views\Page\PageUseControllerView;
use Romchik38\Server\Tests\Unit\Http\Views\Page\WithMetadata\MetaDataService;

use function sprintf;

class PageUseControllerViewTest extends TestCase
{
    public function testWithoutMetadata()
    {
        $pageName           = 'Home';
        $pageDescription    = 'Home page';
        $controller         = new Controller('root');
        $controllerDto      = new DefaultViewDTO($pageName, $pageDescription);
        $controllerTemplate = require_once __DIR__ . '/WithoutMetadata/controllerTemplate.php';
        $generateTemplate   = require_once __DIR__ . '/WithoutMetadata/generateTemplate.php';
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
        $controllerTemplate = require_once __DIR__ . '/WithMetadata/controllerTemplate.php';
        $generateTemplate   = require_once __DIR__ . '/WithMetadata/generateTemplate.php';
        require_once __DIR__ . '/WithMetadata/MetaDataService.php';
        $metaDataService = new MetaDataService($userName);
        $view            = new PageUseControllerView($generateTemplate, $controllerTemplate, $metaDataService);

        $view->setController($controller)->setControllerData($controllerDto);
        $html = $view->toString();

        $expectedTemplate = '<body><p>Hello %s</p><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf($expectedTemplate, $userName, $pageName, $pageDescription);

        $this->assertSame($expectedHtml, $html);
    }
}

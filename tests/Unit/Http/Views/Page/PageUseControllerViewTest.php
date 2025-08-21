<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbInterface;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTO;
use Romchik38\Server\Http\Views\Errors\ViewBuildException;
use Romchik38\Server\Http\Views\Page\PageUseControllerView;
use Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\StaticUrlbuilder\MetaDataService
    as StaticMetadataService;
use Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseControllerView\WithMetadata\MetaDataService;

use function sprintf;

class PageUseControllerViewTest extends TestCase
{
    public function testWithoutMetadata()
    {
        $pageName        = 'Home';
        $pageDescription = 'Home page';
        $controller      = new Controller('root');
        $controllerDto   = new DefaultViewDTO($pageName, $pageDescription);
        $template        = require_once __DIR__ . '/PageUseControllerView/WithoutMetadata/template.php';
        $view            = new PageUseControllerView($template);

        $view->setController($controller)->setControllerData($controllerDto);
        $html = $view->toString();

        $expectedTemplate = '<body><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf($expectedTemplate, $pageName, $pageDescription);

        $this->assertSame($expectedHtml, $html);
    }

    public function testWithMetadata()
    {
        $pageName        = 'Home';
        $pageDescription = 'Home page';
        $userName        = 'User_1';
        $controller      = new Controller('root');
        $controllerDto   = new DefaultViewDTO($pageName, $pageDescription);
        $template        = require __DIR__ . '/PageUseControllerView/WithMetadata/template.php';
        require __DIR__ . '/PageUseControllerView/WithMetadata/MetaDataService.php';
        $metaDataService = new MetaDataService($userName);
        $view            = new PageUseControllerView(
            $template,
            null,
            $metaDataService
        );

        $view->setController($controller)->setControllerData($controllerDto);
        $html = $view->toString();

        $expectedTemplate = '<body><p>Hello %s</p><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf($expectedTemplate, $userName, $pageName, $pageDescription);

        $this->assertSame($expectedHtml, $html);
    }

    public function testToStringThrowsErrorNullController(): void
    {
        $pageName        = 'Home';
        $pageDescription = 'Home page';
        $controllerDto   = new DefaultViewDTO($pageName, $pageDescription);
        $template        = require __DIR__ . '/PageUseControllerView/WithoutMetadata/template.php';
        $view            = new PageUseControllerView($template);
        $view->setControllerData($controllerDto);

        $this->expectException(ViewBuildException::class);
        $view->toString();
    }

    public function testtoStringThrowsErrorNullControllerData(): void
    {
        $template   = require __DIR__ . '/PageUseControllerView/WithoutMetadata/template.php';
        $view       = new PageUseControllerView($template);
        $controller = new Controller('root');
        $view->setController($controller);

        $this->expectException(ViewBuildException::class);
        $view->toString();
    }

    public function testBreadcrumbsDefault(): void
    {
        $pageName        = 'Home';
        $pageDescription = 'Home page';
        $controllerDto   = new DefaultViewDTO($pageName, $pageDescription);
        $template        = require __DIR__ . '/PageUseControllerView/Breadcrumbs/template.php';
        $view            = new PageUseControllerView($template);
        $controller      = new Controller('root');
        $view->setController($controller)->setControllerData($controllerDto);

        $html = $view->toString();

        $expectedTemplate = '<body><p>%s</p><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf(
            $expectedTemplate,
            BreadcrumbInterface::HOME_PLACEHOLDER,
            $pageName,
            $pageDescription
        );

        $this->assertSame($expectedHtml, $html);
    }

    public function testStaticUrlbuilder(): void
    {
        $language        = 'en';
        $pageName        = 'Home';
        $pageDescription = 'Home page';
        $controllerDto   = new DefaultViewDTO($pageName, $pageDescription);
        $template        = require __DIR__ . '/PageUseControllerView/StaticUrlbuilder/template.php';
        require __DIR__ . '/PageUseControllerView/StaticUrlbuilder/MetaDataService.php';
        $view       = new PageUseControllerView(
            $template,
            null,
            new StaticMetadataService($language)
        );
        $controller = new Controller('root');
        $view->setController($controller)->setControllerData($controllerDto);

        $html = $view->toString();

        $expectedTemplate = '<body><p>/%s</p><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf(
            $expectedTemplate,
            $language,
            $pageName,
            $pageDescription
        );

        $this->assertSame($expectedHtml, $html);
    }
}

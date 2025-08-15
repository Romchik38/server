<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Views\Page;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTO;
use Romchik38\Server\Http\Views\Errors\ViewBuildException;
use Romchik38\Server\Http\Views\Page\PageUseSingleView;
use Romchik38\Server\Tests\Unit\Http\Views\Page\PageUseSingleViewTest\WithMetadata\MetaDataService;

use function sprintf;

class PageUseSingleViewTest extends TestCase
{
    public function testWithoutMetadata()
    {
        $pageName         = 'Home';
        $pageDescription  = 'Home page';
        $handlerDto       = new DefaultViewDTO($pageName, $pageDescription);
        $handlerTemplate  = require __DIR__ . '/PageUseSingleViewTest/WithoutMetadata/handlerTemplate.php';
        $generateTemplate = require __DIR__ . '/PageUseSingleViewTest/WithoutMetadata/generateTemplate.php';
        $view             = new PageUseSingleView($generateTemplate, $handlerTemplate);

        $view->setHandlerData($handlerDto);
        $html = $view->toString();

        $expectedTemplate = '<body><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf($expectedTemplate, $pageName, $pageDescription);

        $this->assertSame($expectedHtml, $html);
    }

    public function testWithMetadata()
    {
        $pageName         = 'Home';
        $pageDescription  = 'Home page';
        $userName         = 'User_1';
        $handlerDto       = new DefaultViewDTO($pageName, $pageDescription);
        $handlerTemplate  = require __DIR__ . '/PageUseSingleViewTest/WithMetadata/handlerTemplate.php';
        $generateTemplate = require __DIR__ . '/PageUseSingleViewTest/WithMetadata/generateTemplate.php';
        require_once __DIR__ . '/PageUseSingleViewTest/WithMetadata/MetaDataService.php';
        $metaDataService = new MetaDataService($userName);
        $view            = new PageUseSingleView($generateTemplate, $handlerTemplate, $metaDataService);

        $view->setHandlerData($handlerDto);
        $html = $view->toString();

        $expectedTemplate = '<body><p>Hello %s</p><h1>%s</h1><p>%s</p></body>';
        $expectedHtml     = sprintf($expectedTemplate, $userName, $pageName, $pageDescription);

        $this->assertSame($expectedHtml, $html);
    }

    public function testtoStringThrowsErrorNullHandlerData(): void
    {
        $handlerTemplate  = require __DIR__ . '/PageUseSingleViewTest/WithoutMetadata/handlerTemplate.php';
        $generateTemplate = require __DIR__ . '/PageUseSingleViewTest/WithoutMetadata/generateTemplate.php';
        $view             = new PageUseSingleView($generateTemplate, $handlerTemplate);

        $this->expectException(ViewBuildException::class);
        $view->toString();
    }
}

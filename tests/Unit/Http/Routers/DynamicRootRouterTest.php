<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\ControllersCollection;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;
use Romchik38\Server\Http\Routers\DynamicRootRouter;
use Romchik38\Server\Http\Routers\Errors\RouterProccessErrorException;
use Romchik38\Server\Http\Routers\HttpRouterInterface;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTO;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Services\DynamicRoot\DynamicRootDTO;
use Romchik38\Server\Services\Redirect\Http\Redirect;

class DynamicRootRouterTest extends TestCase
{
    /**
     * #2 comment in the source code
     * Redirect from "/" to "/en"
     */
    public function testExecuteRedirectToDefaultRootFromSlash()
    {
        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $dynamicRootService->expects($this->once())->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->expects($this->once())->method('getRootNames')
            ->willReturn($rootNames);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->execute();
        $this->assertSame('http://example.com/en', $response->getHeaderLine('Location'));
        $this->assertSame(301, $response->getStatusCode());
    }

    /**
     * #3 comment in the source code
     *  Try to redirect from "/path" to "defaultRoot + path"
     */
    public function testExecuteRedirectToDefaultRootPlusPathFromSlashPath()
    {
        $uri     = new Uri('http://example.com/products');
        $request = new ServerRequest([], [], $uri, 'GET');

        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $dynamicRootService->expects($this->once())->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->expects($this->once())->method('getRootNames')
            ->willReturn($rootNames);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->execute();
        $this->assertSame(
            'http://example.com/en/products',
            $response->getHeaderLine('Location')
        );
        $this->assertSame(301, $response->getStatusCode());
    }

    /**
     * #5 comment in the source code
     * Method not Allowed
     */
    public function testExecuteMethodNotAllowed()
    {
        $uri     = new Uri('http://example.com/en/products');
        $request = new ServerRequest([], [], $uri, 'PUT');

        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $controllersCollection->expects($this->once())->method('getMethods')
            ->willReturn(['GET']);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->execute();
        $this->assertSame('GET', $response->getHeaderLine('Allow'));
        $this->assertSame('Method Not Allowed', (string) $response->getBody());
    }

    /**
     * #6. redirect check
     */
    public function testExecuteRedirect()
    {
        $uri     = new Uri('http://example.com/en/products');
        $request = new ServerRequest([], [], $uri, 'GET');

        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto    = new DynamicRootDTO('en');
        $rootNames         = ['en', 'uk'];
        $redirectResultDto = new RedirectResultDTO('/en/newproducts', 301);

        $dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $controllersCollection->method('getController')
            ->willReturn(new Controller('some_name'));

        $redirectService->expects($this->once())->method('execute')
            ->willReturn($redirectResultDto);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection,
            null,
            $redirectService
        );

        $response = $router->execute();
        $this->assertSame(
            'http://example.com/en/newproducts',
            $response->getHeaderLine('Location')
        );
        $this->assertSame(301, $response->getStatusCode());
    }

    /**
     * #7 set current root
     */
    public function testExecuteThrowsRouterProccessError()
    {
        $uri     = new Uri('http://example.com/en/products');
        $request = new ServerRequest([], [], $uri, 'GET');

        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $redirectService->method('execute')->willReturn(null);

        $dynamicRootService->expects($this->once())->method('setCurrentRoot')
            ->with('en')->willReturn(false);

        $this->expectException(RouterProccessErrorException::class);

        $controllersCollection->method('getController')
            ->willReturn(new Controller('some_name'));

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection
        );

        $router->execute();
    }

    /**
     * # 8, 10. Exec
     *
     * return the controller result
     */
    public function testExecuteControllerReturnResult()
    {
        $uri     = new Uri('http://example.com/en/products');
        $request = new ServerRequest([], [], $uri, 'GET');

        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controller            = $this->createMock(Controller::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];
        $response       = new Response();
        $body           = $response->getBody();
        $body->write('Product #1');
        $response = $response->withBody($body);

        $dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $redirectService->method('execute')->willReturn(null);

        $dynamicRootService->expects($this->once())->method('setCurrentRoot')
            ->with('en')->willReturn(true);

        $controllersCollection->method('getController')->willReturn($controller);

        $controller->expects($this->once())->method('execute')
            ->with([ControllerInterface::ROOT_NAME, 'products'])->willReturn($response);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->execute();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Product #1', (string) $response->getBody());
    }

    /**
     * # 11. Show page not found
     *
     * throws not found error
     * without notfoundController
     */
    public function testExecuteControllerThrowsNotFoundErrorWithoutController()
    {
        $uri     = new Uri('http://example.com/en/products');
        $request = new ServerRequest([], [], $uri, 'GET');

        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controller            = $this->createMock(Controller::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $redirectService->method('execute')->willReturn(null);

        $dynamicRootService->method('setCurrentRoot')->willReturn(true);

        $controllersCollection->method('getController')->willReturn($controller);

        $controller->method('execute')->willThrowException(new NotFoundException('not found'));

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->execute();
        $this->assertSame(
            HttpRouterInterface::NOT_FOUND_MESSAGE,
            (string) $response->getBody()
        );
        $this->assertSame(404, $response->getStatusCode());
    }

    /**
     * # 11. Show page not found
     *
     * throws not found error
     * with notfoundController
     */
    public function testExecuteControllerThrowsNotFoundErrorWithController()
    {
        $uri                   = new Uri('http://example.com/en/products');
        $request               = new ServerRequest([], [], $uri, 'GET');
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controller            = $this->createMock(Controller::class);
        $redirectService       = $this->createMock(Redirect::class);
        $notFoundController    = $this->createMock(Controller::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];
        $response       = new Response();
        $body           = $response->getBody();
        $body->write('<h1>Page not found</h1>');
        $response = $response->withBody($body);

        $dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $redirectService->method('execute')->willReturn(null);

        $dynamicRootService->method('setCurrentRoot')->willReturn(true);

        $controllersCollection->method('getController')->willReturn($controller);

        $controller->method('execute')->willThrowException(new NotFoundException('not found'));

        $notFoundController->expects($this->once())->method('execute')
            ->with($this->callback(function ($param) {
                if ([HttpRouterInterface::NOT_FOUND_CONTROLLER_NAME] === $param) {
                    return true;
                } else {
                    return false;
                }
            }))
            ->willReturn($response);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $request,
            $dynamicRootService,
            $controllersCollection,
            $notFoundController
        );

        $response = $router->execute();
        $this->assertSame(
            '<h1>Page not found</h1>',
            (string) $response->getBody()
        );
        $this->assertSame(404, $response->getStatusCode());
    }
}

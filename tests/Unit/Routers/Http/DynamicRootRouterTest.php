<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Routers\Http;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Controllers\ControllerResult;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoot\DynamicRootDTO;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTO;
use Romchik38\Server\Routers\Errors\RouterProccessErrorException;
use Romchik38\Server\Routers\Http\ControllersCollection;
use Romchik38\Server\Routers\Http\DynamicRootRouter;
use Romchik38\Server\Services\DynamicRoot\DynamicRoot;
use Romchik38\Server\Services\Redirect\Http\Redirect;

class DynamicRootRouterTest extends TestCase
{
    protected $routerResult;
    protected $request;
    protected $uri;
    protected $dynamicRootService;
    protected $controller;
    protected $redirectService;
    protected $notFoundController;
    protected $controllersCollection;

    public function setUp(): void
    {
        $this->request               = $this->createMock(ServerRequestInterface::class);
        $this->uri                   = $this->createMock(UriInterface::class);
        $this->dynamicRootService    = $this->createMock(DynamicRoot::class);
        $this->controller            = $this->createMock(Controller::class);
        $this->redirectService       = $this->createMock(Redirect::class);
        $this->notFoundController    = $this->createMock(Controller::class);
        $this->controllersCollection = $this->createMock(ControllersCollection::class);
    }

    /**
     * #2 comment in the source code
     * Redirect from "/" to "/en"
     */
    public function testExecuteRedirectToDefaultRootFromSlash()
    {
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/');
        $defaultRootDTO = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $this->request->expects($this->once())->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->expects($this->once())->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->expects($this->once())->method('getRootNames')
            ->willReturn($rootNames);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection
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
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/products');
        $defaultRootDTO = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $this->request->expects($this->once())->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->expects($this->once())->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->expects($this->once())->method('getRootNames')
            ->willReturn($rootNames);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection
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
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/en/products');
        $defaultRootDTO = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $this->request->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('PUT');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->controllersCollection->expects($this->once())->method('getMethods')
            ->willReturn(['GET']);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection
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
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/en/products');
        $defaultRootDTO    = new DynamicRootDTO('en');
        $rootNames         = ['en', 'uk'];
        $redirectResultDTO = new RedirectResultDTO('/en/newproducts', 301);

        $this->request->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->controllersCollection->method('getController')
            ->willReturn(new Controller('some_name'));

        $this->redirectService->expects($this->once())->method('execute')
            ->willReturn($redirectResultDTO);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection,
            null,
            $this->redirectService
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
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/en/products');
        $defaultRootDTO = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $this->request->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->expects($this->once())->method('setCurrentRoot')
            ->with('en')->willReturn(false);

        $this->expectException(RouterProccessErrorException::class);

        $this->controllersCollection->method('getController')
            ->willReturn(new Controller('some_name'));

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection
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
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/en/products');
        $defaultRootDTO = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];
        $response       = new Response();
        $body           = $response->getBody();
        $body->write('Product #1');
        $response         = $response->withBody($body);
        $controllerResult = new ControllerResult(
            $response,
            ['en', 'products'],
            ActionInterface::TYPE_DEFAULT_ACTION
        );

        $this->request->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->expects($this->once())->method('setCurrentRoot')
            ->with('en')->willReturn(true);

        $this->controllersCollection->method('getController')->willReturn($this->controller);

        $this->controller->expects($this->once())->method('execute')
            ->with([ControllerTreeInterface::ROOT_NAME, 'products'])->willReturn($controllerResult);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection
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
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/en/products');
        $defaultRootDTO = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $this->request->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->method('setCurrentRoot')->willReturn(true);

        $this->controllersCollection->method('getController')->willReturn($this->controller);

        $this->controller->method('execute')->willThrowException(new NotFoundException('not found'));

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection
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
        $this->uri->method('getScheme')->willReturn('http');
        $this->uri->method('getHost')->willReturn('example.com');
        $this->uri->method('getPath')->willReturn('/en/products');
        $defaultRootDTO = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];
        $response       = new Response();
        $body           = $response->getBody();
        $body->write('<h1>Page not found</h1>');
        $response         = $response->withBody($body);
        $controllerResult = new ControllerResult(
            $response,
            [HttpRouterInterface::NOT_FOUND_CONTROLLER_NAME],
            ActionInterface::TYPE_DEFAULT_ACTION
        );

        $this->request->method('getUri')->willReturn($this->uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->method('setCurrentRoot')->willReturn(true);

        $this->controllersCollection->method('getController')->willReturn($this->controller);

        $this->controller->method('execute')->willThrowException(new NotFoundException('not found'));

        $this->notFoundController->expects($this->once())->method('execute')
            ->with($this->callback(function ($param) {
                if ([HttpRouterInterface::NOT_FOUND_CONTROLLER_NAME] === $param) {
                    return true;
                } else {
                    return false;
                }
            }))
            ->willReturn($controllerResult);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $this->request,
            $this->dynamicRootService,
            $this->controllersCollection,
            $this->notFoundController
        );

        $response = $router->execute();
        $this->assertSame(
            '<h1>Page not found</h1>',
            (string) $response->getBody()
        );
        $this->assertSame(404, $response->getStatusCode());
    }
}

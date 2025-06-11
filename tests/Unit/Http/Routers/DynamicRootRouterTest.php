<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllersCollection;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;
use Romchik38\Server\Http\Routers\DynamicRootRouter;
use Romchik38\Server\Http\Routers\Errors\RouterProccessErrorException;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRoot;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootDTO;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Redirect;
use Romchik38\Server\Http\Routers\Handlers\Redirect\RedirectResultDTO;
use Romchik38\Server\Http\Routers\HttpRouterInterface;

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
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->handle($request);
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
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->handle($request);
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
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->handle($request);
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
            $dynamicRootService,
            $controllersCollection,
            null,
            $redirectService
        );

        $response = $router->handle($request);
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
            $dynamicRootService,
            $controllersCollection
        );

        $router->handle($request);
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

        $controller->expects($this->once())->method('handle')
            ->willReturn($response);

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->handle($request);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Product #1', (string) $response->getBody());
    }

    /** @todo refactor */
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

        $controller            = $this->createMock(Controller::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaulRootName     = 'en';
        $rootNames          = ['en', 'uk'];
        $dynamicRootService = new DynamicRoot($defaulRootName, $rootNames);

        $redirectService->method('execute')->willReturn(null);

        $controllersCollection->method('getController')->willReturn($controller);

        $controller->method('handle')->willThrowException(new NotFoundException('not found'));

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $dynamicRootService,
            $controllersCollection
        );

        $response = $router->handle($request);
        $this->assertSame(
            HttpRouterInterface::NOT_FOUND_MESSAGE,
            (string) $response->getBody()
        );
        $this->assertSame(404, $response->getStatusCode());
    }

    /** @todo refactor */
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
        $notFoundHandler       = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new TextResponse('404 page');
            }
        };
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $defaulRootName     = 'en';
        $rootNames          = ['en', 'uk'];
        $dynamicRootService = new DynamicRoot($defaulRootName, $rootNames);

        $redirectService->method('execute')->willReturn(null);

        $controllersCollection->method('getController')->willReturn($controller);

        $controller->method('handle')->willThrowException(new NotFoundException('not found'));

        $router = new DynamicRootRouter(
            new ResponseFactory(),
            $dynamicRootService,
            $controllersCollection,
            $notFoundHandler
        );

        $response = $router->handle($request);
        $this->assertSame(
            '404 page',
            (string) $response->getBody()
        );
        $this->assertSame(404, $response->getStatusCode());
    }
}

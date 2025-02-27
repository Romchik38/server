<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Routers\Http;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\Controller;
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
    /**
     * #2 comment in the source code
     * Redirect from "/" to "/en"
     */
    public function testExecuteRedirectToDefaultRootFromSlash()
    {
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/');
        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

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
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/products');
        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

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
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/en/products');
        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('PUT');

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
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/en/products');
        $defaultRootDto    = new DynamicRootDTO('en');
        $rootNames         = ['en', 'uk'];
        $redirectResultDto = new RedirectResultDTO('/en/newproducts', 301);

        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

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
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/en/products');
        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

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
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controller            = $this->createMock(Controller::class);
        $redirectService       = $this->createMock(Redirect::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/en/products');
        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];
        $response       = new Response();
        $body           = $response->getBody();
        $body->write('Product #1');
        $response = $response->withBody($body);

        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

        $dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDto);

        $dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $redirectService->method('execute')->willReturn(null);

        $dynamicRootService->expects($this->once())->method('setCurrentRoot')
            ->with('en')->willReturn(true);

        $controllersCollection->method('getController')->willReturn($controller);

        $controller->expects($this->once())->method('execute')
            ->with([ControllerTreeInterface::ROOT_NAME, 'products'])->willReturn($response);

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
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controller            = $this->createMock(Controller::class);
        $redirectService       = $this->createMock(Redirect::class);
        $notFoundController    = $this->createMock(Controller::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/en/products');
        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];

        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

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
        $request               = $this->createMock(ServerRequestInterface::class);
        $uri                   = $this->createMock(UriInterface::class);
        $dynamicRootService    = $this->createMock(DynamicRoot::class);
        $controller            = $this->createMock(Controller::class);
        $redirectService       = $this->createMock(Redirect::class);
        $notFoundController    = $this->createMock(Controller::class);
        $controllersCollection = $this->createMock(ControllersCollection::class);

        $uri->method('getScheme')->willReturn('http');
        $uri->method('getHost')->willReturn('example.com');
        $uri->method('getPath')->willReturn('/en/products');
        $defaultRootDto = new DynamicRootDTO('en');
        $rootNames      = ['en', 'uk'];
        $response       = new Response();
        $body           = $response->getBody();
        $body->write('<h1>Page not found</h1>');
        $response = $response->withBody($body);

        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

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

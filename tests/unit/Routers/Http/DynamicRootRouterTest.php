<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Routers\Http\DynamicRootRouter;
use Romchik38\Server\Results\Http\HttpRouterResult;
use Romchik38\Server\Services\Request\Http\Request;
use Romchik38\Server\Services\DymanicRoot\DymanicRoot;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Services\Request\Http\Uri;
use Romchik38\Server\Models\DTO\DymanicRoot\DymanicRootDTO;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTO;
use Romchik38\Server\Results\Controller\ControllerResult;
use Romchik38\Server\Routers\Errors\RouterProccessError;
use Romchik38\Server\Services\Redirect\Http\Redirect;
use Romchik38\Server\Api\Routers\Http\RouterHeadersInterface;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Routers\Http\DynamicHeadersCollection;
use Romchik38\Server\Routers\Http\RouterHeader;

class DynamicRootRouterTest extends TestCase
{

    protected $routerResult;
    protected $request;
    protected $dynamicRootService;
    protected $controller;
    protected $redirectService;
    protected $header;
    protected $notFoundController;

    public function setUp(): void
    {
        $this->routerResult = $this->createMock(HttpRouterResult::class);
        $this->request = $this->createMock(Request::class);
        $this->dynamicRootService = $this->createMock(DymanicRoot::class);
        $this->controller = $this->createMock(Controller::class);
        $this->redirectService = $this->createMock(Redirect::class);
        $this->notFoundController = $this->createMock(Controller::class);
    }

    /**
     * #2 comment in the source code
     * Redirect from "/" to "/en"
     */
    public function testExecuteRedirectToDefaultRootFromSlash()
    {
        $uri = new Uri('http', 'example.com', '/');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];

        $this->request->expects($this->once())->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->expects($this->once())->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->expects($this->once())->method('getRootNames')
            ->willReturn($rootNames);

        $this->routerResult->expects($this->once())->method('setHeaders')
            ->with([['Location: http://example.com/en', true, 301]]);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            ['GET' => $this->controller]
        );

        $router->execute();
    }

    /**
     * #3 comment in the source code
     *  Try to redirect from "/path" to "defaultRoot + path"
     */
    public function testExecuteRedirectToDefaultRootPlusPathFromSlashPath()
    {
        $uri = new Uri('http', 'example.com', '/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];

        $this->request->expects($this->once())->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->expects($this->once())->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->expects($this->once())->method('getRootNames')
            ->willReturn($rootNames);

        $this->routerResult->expects($this->once())->method('setHeaders')
            ->with([['Location: http://example.com/en/products', true, 301]]);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            ['GET' => $this->controller]
        );

        $router->execute();
    }

    /**
     * #5 comment in the source code
     * Method not Allowed
     */

    public function testExecuteMethodNotAllowed()
    {
        $uri = new Uri('http', 'example.com', '/en/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];

        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('PUT');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->routerResult->expects($this->once())->method('setResponse')
            ->with(HttpRouterResultInterface::METHOD_NOT_ALLOWED_RESPONSE)
            ->willReturn($this->routerResult);

        $this->routerResult->expects($this->once())->method('setHeaders')
            ->with([['Allow:GET', true, HttpRouterResultInterface::METHOD_NOT_ALLOWED_CODE]]);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            [function () {
                return ['GET' => $this->controller];
            }]
        );

        $router->execute();
    }

    /**
     * #6. redirect check
     */

    public function testExecuteRedirect()
    {
        $uri = new Uri('http', 'example.com', '/en/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];
        $redirectResultDTO = new RedirectResultDTO('/en/newproducts', 301);

        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->expects($this->once())->method('execute')
            ->willReturn($redirectResultDTO);

        $this->routerResult->expects($this->once())->method('setHeaders')
            ->with([['Location: http://example.com/en/newproducts', true, 301]]);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            [function () {
                return ['GET' => $this->controller];
            }],
            [],
            null,
            $this->redirectService
        );

        $router->execute();
    }

    /**
     * #7 set current root
     */
    public function testExecuteThrowsRouterProccessError()
    {
        $uri = new Uri('http', 'example.com', '/en/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];

        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->expects($this->once())->method('setCurrentRoot')
            ->with('en')->willReturn(false);

        $this->expectException(RouterProccessError::class);

        // $this->routerResult->expects($this->once())->method('setHeaders')
        //     ->with([['Location: http://example.com/en/newproducts', true, 301]]);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            [function () {
                return ['GET' => $this->controller];
            }]
        );

        $router->execute();
    }

    /**
     * # 8, 10. Exec
     *   
     * without headers
     * return the result
     */
    public function testExecuteControllerReturnResultWithoutHeaders()
    {
        $uri = new Uri('http', 'example.com', '/en/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];
        $controllerResult = new ControllerResult(
            'Product #1',
            ['en', 'products'],
            ActionInterface::TYPE_ACTION
        );

        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->expects($this->once())->method('setCurrentRoot')
            ->with('en')->willReturn(true);

        $this->controller->expects($this->once())->method('execute')
            ->with(['en', 'products'])->willReturn($controllerResult);

        $this->routerResult->expects($this->once())->method('setStatusCode')
            ->with(200)->willReturn($this->routerResult);

        $this->routerResult->expects($this->once())->method('setResponse')
            ->with('Product #1');

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            [function () {
                return ['GET' => $this->controller];
            }]
        );

        $router->execute();
    }

    /**
     * # 9. Set headers
     *   
     * with headers
     * return the result
     */
    public function testExecuteControllerReturnResultWithHeaders()
    {
        $uri = new Uri('http', 'example.com', '/en/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];
        $path = ['en', 'products'];
        $controllerResult = new ControllerResult(
            'Product #1',
            $path,
            ActionInterface::TYPE_ACTION
        );

        $this->header = new class('en<>products', 'GET') extends RouterHeader {
            public function setHeaders(HttpRouterResultInterface $result, array $path): void
            {
                $result->setHeaders([
                    ['Cache-Control:no-cache']
                ]);
            }
        };

        $data = [$this->header];

        $headerService = new DynamicHeadersCollection($data);

        $headers = [fn() => $headerService];

        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->method('setCurrentRoot')->willReturn(true);

        $this->controller->method('execute')->willReturn($controllerResult);

        $this->routerResult->method('setStatusCode')->willReturn($this->routerResult);

        $this->routerResult->expects($this->once())->method('setHeaders')->with([
            ['Cache-Control:no-cache']
        ]);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            [function () {
                return ['GET' => $this->controller];
            }],
            $headers
        );

        $router->execute();
    }


    /**
     * # 11. Show page not found
     *   
     * throws not found error
     * without notfoundController
     */
    public function testExecuteControllerThrowsNotFoundErrorWithoutController()
    {
        $uri = new Uri('http', 'example.com', '/en/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];

        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->method('setCurrentRoot')->willReturn(true);

        $this->controller->method('execute')->willThrowException(new NotFoundException('not found'));


        $this->routerResult->expects($this->once())->method('setStatusCode')
            ->with(HttpRouterResultInterface::NOT_FOUND_STATUS_CODE)
            ->willReturn($this->routerResult);

        $this->routerResult->expects($this->once())->method('setResponse')
            ->with(HttpRouterResultInterface::NOT_FOUND_RESPONSE)
            ->willReturn($this->routerResult);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            [function () {
                return ['GET' => $this->controller];
            }]
        );

        $router->execute();
    }

    /**
     * # 11. Show page not found
     *   
     * throws not found error
     * with notfoundController
     */
    public function testExecuteControllerThrowsNotFoundErrorWithController()
    {
        $uri = new Uri('http', 'example.com', '/en/products');
        $defaultRootDTO = new DymanicRootDTO('en');
        $rootNames = ['en', 'uk'];
        $notFoundResponse = '<h1>Page not found</h1>';

        $controllerResult = new ControllerResult(
            $notFoundResponse,
            ['404'],
            ActionInterface::TYPE_ACTION
        );

        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $this->dynamicRootService->method('getDefaultRoot')
            ->willReturn($defaultRootDTO);

        $this->dynamicRootService->method('getRootNames')
            ->willReturn($rootNames);

        $this->redirectService->method('execute')->willReturn(null);

        $this->dynamicRootService->method('setCurrentRoot')->willReturn(true);

        $this->controller->method('execute')->willThrowException(new NotFoundException('not found'));


        $this->routerResult->method('setStatusCode')->willReturn($this->routerResult);

        $this->routerResult->expects($this->once())->method('setResponse')
            ->with($notFoundResponse)
            ->willReturn($this->routerResult)
        ;

        $this->notFoundController->expects($this->once())->method('execute')
            ->willReturn($controllerResult);

        $router = new DynamicRootRouter(
            $this->routerResult,
            $this->request,
            $this->dynamicRootService,
            [function () {
                return ['GET' => $this->controller];
            }],
            [],
            $this->notFoundController
        );

        $router->execute();
    }
}
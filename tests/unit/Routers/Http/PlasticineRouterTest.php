<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTO;
use Romchik38\Server\Routers\Http\PlasticineRouter;
use Romchik38\Server\Results\Http\HttpRouterResult;
use Romchik38\Server\Routers\Http\ControllersCollection;
use Romchik38\Server\Services\Redirect\Http\Redirect;
use Romchik38\Server\Services\Request\Http\Request;
use Romchik38\Server\Services\Request\Http\Uri;


class PlasticineRouterTest extends TestCase
{

    protected $routerResult;
    protected ControllersCollection $controllerCollection;
    protected $controller;
    protected $notFoundController = null;
    protected $redirectService = null;
    protected $request;

    public function setUp(): void
    {
        $this->routerResult = $this->createMock(HttpRouterResult::class);
        $this->controller = $this->createMock(Controller::class);
        $this->controllerCollection = new ControllersCollection;
        $this->request = $this->createMock(Request::class);
        $this->request = $this->createMock(Request::class);
    }
    public function testExecuteRedirect()
    {
        $this->controllerCollection->setController(
            $this->controller,
            HttpRouterInterface::REQUEST_METHOD_GET
        );
        $uri = new Uri('http', 'example.com', '/index');
        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getMethod')->willReturn('GET');

        $redirectLocation = 'http://example.com/';
        $redirectStatusCode = 301;
        $redirectResultDTO = new RedirectResultDTO($redirectLocation, $redirectStatusCode);
        $this->redirectService = $this->createMock(Redirect::class);

        $this->redirectService->expects($this->once())->method('execute')
            ->with('/index', 'GET')->willReturn($redirectResultDTO);

        // so we will expects this in rouerResult
        $this->routerResult->expects($this->once())->method('setHeaders')
            ->with([
                [
                    'Location: ' . $redirectLocation,
                    true,
                    $redirectStatusCode
                ]
            ]);

        $router = new PlasticineRouter(
            $this->routerResult,
            $this->controllerCollection,
            $this->request,
            null,
            $this->notFoundController,
            $this->redirectService
        );

        $router->execute();
    }
}

<?php

declare(strict_types=1);

use Laminas\Diactoros\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Models\DTO\RedirectResult\Http\RedirectResultDTO;
use Romchik38\Server\Routers\Http\PlasticineRouter;
use Romchik38\Server\Routers\Http\ControllersCollection;
use Romchik38\Server\Services\Redirect\Http\Redirect;

final class PlasticineRouterTest extends TestCase
{
    protected $routerResult;
    protected ControllersCollection $controllerCollection;
    protected $controller;
    protected $notFoundController = null;
    protected $redirectService = null;

    public function setUp(): void
    {
        $this->controller = $this->createMock(Controller::class);
        $this->controllerCollection = new ControllersCollection;
    }

    /** 1. method check  */
    public function testRootControllerNotFound(): void
    {
        $rootController = new Controller('root');
        $this->controllerCollection->setController(
            $rootController,
            HttpRouterInterface::REQUEST_METHOD_GET
        );

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/index');
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('POST');


        $router = new PlasticineRouter(
            new ResponseFactory,
            $this->controllerCollection,
            $request
        );

        $response = $router->execute();

        $headers = $response->getHeaders();
        $this->assertSame(1, count($headers));

        $this->assertSame('GET', $response->getHeaderLine('Allow'));
    }

    // 2. redirect check
    public function testExecuteRedirect()
    {
        $this->controllerCollection->setController(
            $this->controller,
            HttpRouterInterface::REQUEST_METHOD_GET
        );
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/index');
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

        $redirectLocation = 'http://example.com/';
        $redirectStatusCode = 301;
        $redirectResultDTO = new RedirectResultDTO($redirectLocation, $redirectStatusCode);
        $this->redirectService = $this->createMock(Redirect::class);

        $this->redirectService->expects($this->once())->method('execute')
            ->with('/index', 'GET')->willReturn($redirectResultDTO);

        $router = new PlasticineRouter(
            new ResponseFactory,
            $this->controllerCollection,
            $request,
            $this->notFoundController,
            $this->redirectService
        );

        $response = $router->execute();

        $this->assertSame($redirectLocation, $response->getHeaderLine('Location'));
        $this->assertSame($redirectStatusCode, $response->getStatusCode());
    }

    // 4. Exec
    public function testExecute(): void
    {
        require_once __DIR__ . '/PlasticineRouterTest/Root/DefaultAction.php';
        $rootController = new Controller(
            'root',
            true,
            new DefaultAction
        );
        $this->controllerCollection->setController(
            $rootController,
            HttpRouterInterface::REQUEST_METHOD_GET
        );

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/');
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUri')->willReturn($uri);
        $request->method('getMethod')->willReturn('GET');

        $router = new PlasticineRouter(
            new ResponseFactory,
            $this->controllerCollection,
            $request
        );

        $response = $router->execute();
        $statusCode = $response->getStatusCode();

        $this->assertSame('hello world', (string) $response->getBody());
        $this->assertSame(200, $statusCode);
    }

}

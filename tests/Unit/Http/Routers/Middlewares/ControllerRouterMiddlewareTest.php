<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllersCollection;
use Romchik38\Server\Http\Routers\Middlewares\ControllerRouterMiddleware;
use Romchik38\Server\Http\Routers\Middlewares\DefaultPathRouterMiddleware;
use Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers\DefaultAction;

final class ControllerRouterMiddlewareTest extends TestCase
{
    public function testHandle(): void
    {
        $attributePathName = 'default';
        // action
        $responsePhrase = 'hello world';
        $action         = new DefaultAction($responsePhrase);
        $rootController = new Controller('root', true, $action);
        // controller middleware
        $controllerCollection = new ControllersCollection();
        $controllerCollection->setGetController($rootController);
        $controllerMiddleware = new ControllerRouterMiddleware(
            $controllerCollection,
            new ResponseFactory(),
            $attributePathName
        );

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        // path middleware
        $pathMiddleware  = new DefaultPathRouterMiddleware($attributePathName);
        $pathResult      = $pathMiddleware($request);
        $requestWithPath = $request->withAttribute($attributePathName, $pathResult);

        $response = $controllerMiddleware($requestWithPath);
        $body     = $response->getBody();

        $this->assertSame($responsePhrase, (string) $body);
    }

    public function testHandleReturnsNullOnNotFound(): void
    {
        $attributePathName = 'default';
        // action
        $responsePhrase = 'hello world';
        $action         = new DefaultAction($responsePhrase);
        $rootController = new Controller('root', true, $action);
        // controller middleware
        $controllerCollection = new ControllersCollection();
        $controllerCollection->setGetController($rootController);
        $controllerMiddleware = new ControllerRouterMiddleware(
            $controllerCollection,
            new ResponseFactory(),
            $attributePathName
        );

        $uri     = new Uri('http://example.com/other');
        $request = new ServerRequest([], [], $uri, 'GET');

        // path middleware
        $pathMiddleware  = new DefaultPathRouterMiddleware($attributePathName);
        $pathResult      = $pathMiddleware($request);
        $requestWithPath = $request->withAttribute($attributePathName, $pathResult);

        $response = $controllerMiddleware($requestWithPath);
        $this->assertNull($response);
    }

    public function testHandleReturnsNullOnMissedPath(): void
    {
        $attributePathName = 'default';
        // action
        $responsePhrase = 'hello world';
        $action         = new DefaultAction($responsePhrase);
        $rootController = new Controller('root', true, $action);
        // controller middleware
        $controllerCollection = new ControllersCollection();
        $controllerCollection->setGetController($rootController);
        $controllerMiddleware = new ControllerRouterMiddleware(
            $controllerCollection,
            new ResponseFactory(),
            $attributePathName
        );

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $response = $controllerMiddleware($request);
        $this->assertNull($response);
    }

    public function testHandleReturnsNotAllowed(): void
    {
        $attributePathName = 'default';
        // action
        $responsePhrase = 'hello world';
        $action         = new DefaultAction($responsePhrase);
        $rootController = new Controller('root', true, $action);
        // controller middleware
        $controllerCollection = new ControllersCollection();
        $controllerCollection->setGetController($rootController);
        $controllerMiddleware = new ControllerRouterMiddleware(
            $controllerCollection,
            new ResponseFactory(),
            $attributePathName
        );

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'POST');

        // path middleware
        $pathMiddleware  = new DefaultPathRouterMiddleware($attributePathName);
        $pathResult      = $pathMiddleware($request);
        $requestWithPath = $request->withAttribute($attributePathName, $pathResult);

        $response   = $controllerMiddleware($requestWithPath);
        $body       = $response->getBody();
        $headerLine = $response->getHeaderLine('Allow');

        $this->assertSame('Method Not Allowed', (string) $body);
        $this->assertSame('GET', $headerLine);
    }

    public function testHandleReturnsEmptyBodyOnHead(): void
    {
        $attributePathName = 'default';
        // action
        $responsePhrase = 'hello world';
        $action         = new DefaultAction($responsePhrase);
        $rootController = new Controller('root', true, $action);
        // controller middleware
        $controllerCollection = new ControllersCollection();
        $controllerCollection->setGetController($rootController);
        $controllerMiddleware = new ControllerRouterMiddleware(
            $controllerCollection,
            new ResponseFactory(),
            $attributePathName
        );

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'HEAD');

        // path middleware
        $pathMiddleware  = new DefaultPathRouterMiddleware($attributePathName);
        $pathResult      = $pathMiddleware($request);
        $requestWithPath = $request->withAttribute($attributePathName, $pathResult);

        $response = $controllerMiddleware($requestWithPath);
        $body     = $response->getBody();

        $this->assertSame('', (string) $body);
    }

    public function testHandleReturnsNullOnEmptyCollection(): void
    {
        $attributePathName = 'default';
        // controller middleware
        $controllerCollection = new ControllersCollection();
        $controllerMiddleware = new ControllerRouterMiddleware(
            $controllerCollection,
            new ResponseFactory(),
            $attributePathName
        );

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        // path middleware
        $pathMiddleware  = new DefaultPathRouterMiddleware($attributePathName);
        $pathResult      = $pathMiddleware($request);
        $requestWithPath = $request->withAttribute($attributePathName, $pathResult);

        $response = $controllerMiddleware($requestWithPath);

        $this->assertNull($response);
    }
}

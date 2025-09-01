<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\PathInterface;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRoot;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Http\Routers\Middlewares\DynamicPathRouterMiddleware;
use Romchik38\Server\Http\Routers\Middlewares\Result\DynamicPathMiddlewareResult;

use function count;

final class DynamicPathRouterMiddlewareTest extends TestCase
{
    public function testHandle(): void
    {
        $dynamicRoot     = new DynamicRoot('en', ['en', 'uk']);
        $responseFactory = new ResponseFactory();
        $middleware      = new DynamicPathRouterMiddleware($dynamicRoot, $responseFactory);

        $uri     = new Uri('http://example.com/en');
        $request = new ServerRequest([], [], $uri, 'GET');

        $result = $middleware($request);

        $this->assertTrue($result instanceof DynamicPathMiddlewareResult);

        $dynamicRootResult = $result->getDynamicRoot();
        $rootList          = $dynamicRootResult->getRootList();
        [$enRoot, $ukRoot] = $rootList;
        $path              = $result->getPath();

        $this->assertTrue($dynamicRootResult instanceof DynamicRootInterface);
        $this->assertTrue($path instanceof PathInterface);

        $this->assertSame('en', $dynamicRootResult->getCurrentRoot()->getName());
        $this->assertSame(2, count($rootList));
        $this->assertSame('en', $enRoot->getName());
        $this->assertSame('uk', $ukRoot->getName());
        $this->assertSame('en', $dynamicRootResult->getDefaultRoot()->getName());
        $this->assertTrue($dynamicRootResult !== $dynamicRoot);

        $this->assertSame(['root'], $path());
    }

    public function testHandleReturnsNullOnTrailingSlash(): void
    {
        $dynamicRoot     = new DynamicRoot('en', ['en', 'uk']);
        $responseFactory = new ResponseFactory();
        $middleware      = new DynamicPathRouterMiddleware($dynamicRoot, $responseFactory);

        $uri     = new Uri('http://example.com/en/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $result = $middleware($request);

        $this->assertNull($result);
    }

    public function testHandleReturnsRedirectOnDefaultRoot(): void
    {
        $dynamicRoot     = new DynamicRoot('en', ['en', 'uk']);
        $responseFactory = new ResponseFactory();
        $middleware      = new DynamicPathRouterMiddleware($dynamicRoot, $responseFactory);

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $result     = $middleware($request);
        $headerLine = $result->getHeaderLine('Location');

        $this->assertSame('http://example.com/en', $headerLine);
    }

    public function testWithPrefferedRootFound(): void
    {
        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');
        $request = $request->withAttribute(
            'preffered_root_router_middleware',
            ['uk-UA', 'uk', 'en-US', 'en']
        );

        $dynamicRoot     = new DynamicRoot('en', ['en', 'uk']);
        $responseFactory = new ResponseFactory();
        $middleware      = new DynamicPathRouterMiddleware($dynamicRoot, $responseFactory);

        $result = $middleware($request);

        $headerLine = $result->getHeaderLine('Location');
        $this->assertSame('http://example.com/uk', $headerLine);
    }

    public function testWithPrefferedRootNotFound(): void
    {
        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');
        $request = $request->withAttribute(
            'preffered_root_router_middleware',
            ['uk-UA', 'uk']
        );

        $dynamicRoot     = new DynamicRoot('en', ['en', 'fr']);
        $responseFactory = new ResponseFactory();
        $middleware      = new DynamicPathRouterMiddleware($dynamicRoot, $responseFactory);

        $result = $middleware($request);

        $headerLine = $result->getHeaderLine('Location');
        $this->assertSame('http://example.com/en', $headerLine);
    }
}

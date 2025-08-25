<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Routers\MiddlewareRouter;
use Romchik38\Server\Http\Routers\Middlewares\HandlerRouterMiddleware;
use Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\HandlerRouterMiddlewareTest\NullMiddleware;
use Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\HandlerRouterMiddlewareTest\ResultMiddleware;
use Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers\AttributeHandler;
use Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers\TextResponseHandler;
use RuntimeException;

final class MiddlewareRouterTest extends TestCase
{
    public function testSingleHandleMiddleware(): void
    {
        $responsePhrase = 'hello world';
        $handler        = new TextResponseHandler($responsePhrase);
        $middleware     = new HandlerRouterMiddleware($handler);

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $router   = new MiddlewareRouter($middleware);
        $response = $router->handle($request);
        $body     = $response->getBody();

        $this->assertSame($responsePhrase, (string) $body);
    }

    public function testMiddlewareReturnsNull(): void
    {
        $middleware = new NullMiddleware();

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $router = new MiddlewareRouter($middleware);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Router middleware does not return a response');
        $router->handle($request);
    }

    public function testMiddlewareReturnsResult(): void
    {
        // 1 middleware
        $result        = 'some_result';
        $attributeName = 'result_middleware';
        $middleware    = new ResultMiddleware($result, $attributeName);

        // 2 middleware
        $handler           = new AttributeHandler($attributeName);
        $handlerMiddleware = new HandlerRouterMiddleware($handler);

        // chain
        $middleware->setNext($handlerMiddleware);

        // request
        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        // router
        $router = new MiddlewareRouter($middleware);

        $response = $router->handle($request);

        $body = $response->getBody();
        $this->assertSame($result, (string) $body);
    }
}

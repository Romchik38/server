<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Controller\PathInterface;
use Romchik38\Server\Http\Routers\Middlewares\DefaultPathRouterMiddleware;
use Romchik38\Server\Http\Routers\Middlewares\Result\DefaultPathMiddlewareResult;

final class DefaultPathRouterMiddlewareTest extends TestCase
{
    public function testHandle(): void
    {
        $middleware = new DefaultPathRouterMiddleware();

        $uri     = new Uri('http://example.com/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $result = $middleware($request);

        $this->assertTrue($result instanceof DefaultPathMiddlewareResult);

        $path = $result->getPath();

        $this->assertTrue($path instanceof PathInterface);
        $this->assertSame(['root'], $path());
    }

    public function testHandleReturnsNull(): void
    {
        $middleware = new DefaultPathRouterMiddleware();

        $uri     = new Uri('http://example.com/some-route/');
        $request = new ServerRequest([], [], $uri, 'GET');

        $result = $middleware($request);

        $this->assertNull($result);
    }
}

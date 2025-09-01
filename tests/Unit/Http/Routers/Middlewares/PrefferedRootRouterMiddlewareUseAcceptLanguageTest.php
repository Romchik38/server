<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Routers\Middlewares\PrefferedRootRouterMiddlewareUseAcceptLanguage;

final class PrefferedRootRouterMiddlewareUseAcceptLanguageTest extends TestCase
{
    public function testWithoutAll(): void
    {
        $m          = new PrefferedRootRouterMiddlewareUseAcceptLanguage();
        $uri        = new Uri('http://example.com');
        $request    = new ServerRequest([], [], $uri, 'GET');
        $headerLine = 'uk-UA,uk;q=0.8,en-US;q=0.5,en;q=0.3';
        $request    = $request->withHeader('Accept-Language', $headerLine);

        $result = $m($request);
        $this->assertSame(['uk-UA', 'uk', 'en-US', 'en'], $result);
    }

    public function testWithAll(): void
    {
        $m          = new PrefferedRootRouterMiddlewareUseAcceptLanguage();
        $uri        = new Uri('http://example.com');
        $request    = new ServerRequest([], [], $uri, 'GET');
        $headerLine = 'uk-UA,uk;q=0.8,en-US;q=0.5,en;q=0.3,*/*;q=0.2';
        $request    = $request->withHeader('Accept-Language', $headerLine);

        $result = $m($request);
        $this->assertSame(['uk-UA', 'uk', 'en-US', 'en'], $result);
    }

    public function testOnlyWithAll(): void
    {
        $m          = new PrefferedRootRouterMiddlewareUseAcceptLanguage();
        $uri        = new Uri('http://example.com');
        $request    = new ServerRequest([], [], $uri, 'GET');
        $headerLine = '*/*';
        $request    = $request->withHeader('Accept-Language', $headerLine);

        $result = $m($request);
        $this->assertSame([], $result);
    }

    public function testWithOne(): void
    {
        $m          = new PrefferedRootRouterMiddlewareUseAcceptLanguage();
        $uri        = new Uri('http://example.com');
        $request    = new ServerRequest([], [], $uri, 'GET');
        $headerLine = 'en';
        $request    = $request->withHeader('Accept-Language', $headerLine);

        $result = $m($request);
        $this->assertSame(['en'], $result);
    }

    public function testWithoutPriority(): void
    {
        $m          = new PrefferedRootRouterMiddlewareUseAcceptLanguage();
        $uri        = new Uri('http://example.com');
        $request    = new ServerRequest([], [], $uri, 'GET');
        $headerLine = 'en,uk,fr';
        $request    = $request->withHeader('Accept-Language', $headerLine);

        $result = $m($request);
        $this->assertSame(['en', 'uk', 'fr'], $result);
    }
}

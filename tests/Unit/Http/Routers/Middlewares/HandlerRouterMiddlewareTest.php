<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Routers\Middlewares;

use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Romchik38\Server\Http\Routers\Middlewares\HandlerRouterMiddleware;
use Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers\AttributeHandler;
use Romchik38\Server\Tests\Unit\Http\Routers\Middlewares\Handlers\TextResponseHandler;

final class HandlerRouterMiddlewareTest extends TestCase
{
    public function testHandle(): void
    {
        $responsePhrase = 'hello world';
        $handler        = new TextResponseHandler($responsePhrase);
        $middleware     = new HandlerRouterMiddleware($handler);

        $uri     = new Uri('http://example.com/contacts');
        $request = new ServerRequest([], [], $uri, 'GET');

        $response = $middleware($request);
        $body     = $response->getBody();

        $this->assertSame($responsePhrase, (string) $body);
    }

    public function testHandleWithAttributes(): void
    {
        $attributeName  = 'attribute_1';
        $attributeValue = 'value_1';
        $handler        = new AttributeHandler($attributeName);
        $middleware     = new HandlerRouterMiddleware($handler);

        $uri     = new Uri('http://example.com/contacts');
        $request = new ServerRequest([], [], $uri, 'GET');
        $request = $request->withAttribute($attributeName, $attributeValue);

        $response = $middleware($request);
        $body     = $response->getBody();

        $this->assertSame($attributeValue, (string) $body);
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Middleware\RequestMiddlewareInterface;
use Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest\RequestMiddlewareTest\AbstractRequestMiddleware;
use Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest\RequestMiddlewareTest\RootDefaultAction;
use Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest\RequestMiddlewareTest\RootDefaultActionResult;

final class RequestMiddlewareTest extends TestCase
{
    public function testRequestMiddlewareReturnNull(): void
    {
        $rootDefaultAction = new RootDefaultAction();

        $middleware = new class extends AbstractRequestMiddleware implements RequestMiddlewareInterface
        {
            public function __invoke(ServerRequestInterface $request): ?ResponseInterface
            {
                return null;
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root->addRequestMiddleware($middleware);

        $elements = ['root'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);
        $this->assertSame('<h1>Home page<h1>', (string) $response->getBody());
    }

    public function testRequestMiddlewareReturnResponse(): void
    {
        $rootDefaultAction = new RootDefaultAction();

        $middleware = new class extends AbstractRequestMiddleware implements RequestMiddlewareInterface
        {
            public function __invoke(ServerRequestInterface $request): ?ResponseInterface
            {
                return new HtmlResponse('from middleware');
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root->addRequestMiddleware($middleware);

        $elements = ['root'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);
        $this->assertSame('from middleware', (string) $response->getBody());
    }

    public function testRequestMiddlewareReturnsResult(): void
    {
        $rootDefaultAction = new RootDefaultActionResult();

        $middleware = new class extends AbstractRequestMiddleware implements RequestMiddlewareInterface
        {
            public function __invoke(ServerRequestInterface $request): string
            {
                return 'user_1';
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root->addRequestMiddleware($middleware);

        $elements = ['root'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);
        $this->assertSame('<h1>Hello user user_1<h1>', (string) $response->getBody());
    }

    public function testRequestMiddlewareReturnResponseWithAfewMiddlewares(): void
    {
        $rootDefaultAction = new RootDefaultAction();

        $middleware1 = new class extends AbstractRequestMiddleware implements RequestMiddlewareInterface
        {
            public function __invoke(ServerRequestInterface $request): ?ResponseInterface
            {
                return null;
            }
        };

        $middleware2 = new class extends AbstractRequestMiddleware implements RequestMiddlewareInterface
        {
            public function __invoke(ServerRequestInterface $request): ?ResponseInterface
            {
                return new HtmlResponse('from middleware2');
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root
            ->addRequestMiddleware($middleware1)
            ->addRequestMiddleware($middleware2);

        $elements = ['root'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);
        $this->assertSame('from middleware2', (string) $response->getBody());
    }
}

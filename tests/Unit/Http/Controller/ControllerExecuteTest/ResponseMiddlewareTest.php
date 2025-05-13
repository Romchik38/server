<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;
use Romchik38\Server\Http\Controller\Actions\DynamicActionInterface;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Errors\ActionNotFoundException;
use Romchik38\Server\Http\Controller\Errors\DynamicActionLogicException;
use Romchik38\Server\Http\Controller\Middleware\ResponseMiddlewareInterface;

use function array_keys;
use function sprintf;

final class ResponseMiddlewareTest extends TestCase
{
    /**
     * Default Action
     */
    public function testResponseMiddlewareChangeHeaders(): void
    {
        $rootDefaultAction = $this->createDefaultAction();

        $middleware = new class implements ResponseMiddlewareInterface
        {
            public function __invoke(ResponseInterface $response): ResponseInterface
            {
                return $response->withHeader('Content-Type', 'application/json');
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root->addResponseMiddleware($middleware);

        $elements = ['root'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertSame('<h1>Home page<h1>', (string) $response->getBody());
    }

     /**
      * Default Action
      */
    public function testResponseMiddlewareChangeHeadersTwice(): void
    {
        $rootDefaultAction = $this->createDefaultAction();

        $middleware1 = new class implements ResponseMiddlewareInterface
        {
            public function __invoke(ResponseInterface $response): ResponseInterface
            {
                return $response->withHeader('Content-Type', 'application/json');
            }
        };

        $middleware2 = new class implements ResponseMiddlewareInterface
        {
            public function __invoke(ResponseInterface $response): ResponseInterface
            {
                return $response->withHeader('Cache-Control', 'max-age=600');
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root
            ->addResponseMiddleware($middleware1)
            ->addResponseMiddleware($middleware2);

        $elements = ['root'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);
        $this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertSame('max-age=600', $response->getHeaderLine('Cache-Control'));
        $this->assertSame('<h1>Home page<h1>', (string) $response->getBody());
    }

    /**
     * Next Controller
     */
    public function testResponseMiddlewareAfterNextController(): void
    {
        $middleware = new class implements ResponseMiddlewareInterface
        {
            public function __invoke(ResponseInterface $response): ResponseInterface
            {
                return $response->withHeader('Cache-Control', 'max-age=6001');
            }
        };

        $root = new Controller(
            'root',
            true
        );

        $homeDefaultAction = $this->createDefaultAction();
        $homePage          = new Controller(
            'homepage',
            true,
            $homeDefaultAction
        );

        $root
            ->setChild($homePage)
            ->addResponseMiddleware($middleware);

        $elements = ['root', 'homepage'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);

        $this->assertSame('max-age=6001', $response->getHeaderLine('Cache-Control'));
        $this->assertSame('<h1>Home page<h1>', (string) $response->getBody());
    }

    /**
     * Dynamic Action
     */
    public function testResponseMiddlewareChangeAfterDynamicAction(): void
    {
        $dynamicAction = $this->createDynamicAction();

        $middleware = new class implements ResponseMiddlewareInterface
        {
            public function __invoke(ResponseInterface $response): ResponseInterface
            {
                return $response->withHeader('Cache-Control', 'no-cache');
            }
        };

        $root = new Controller(
            'root',
            true,
            null,
            $dynamicAction
        );

        $root->addResponseMiddleware($middleware);

        $elements = ['root', 'about'];
        $uri      = new Uri('http://example.com');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);

        $this->assertSame('no-cache', $response->getHeaderLine('Cache-Control'));
        $this->assertSame('Response from About', (string) $response->getBody());
    }

    private function createDefaultAction(): DefaultActionInterface
    {
        return new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('<h1>Home page<h1>');
                $response = $response->withBody($body);
                return $response;
            }

            public function getDescription(): string
            {
                return 'Home';
            }
        };
    }

    private function createDynamicAction(): DynamicActionInterface
    {
        return new class extends AbstractAction implements DynamicActionInterface {
            /** @var array<string,string> $routes */
            protected array $routes = [
                'about' => 'About',
            ];

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $route = $request->getAttribute(self::TYPE_DYNAMIC_ACTION);
                $route = $this->routes[$route] ?? null;
                if ($route === null) {
                    throw new ActionNotFoundException(
                        sprintf('Route %s not found', $route)
                    );
                }
                $response = new Response();
                $body     = $response->getBody();
                $body->write('Response from ' . $route);
                $response = $response->withBody($body);
                return $response;
            }

            public function getDescription(string $route): string
            {
                $description = $this->routes[$route] ?? null;
                if ($description === null) {
                    throw new DynamicActionLogicException(
                        sprintf('description for route %s not exist', $route)
                    );
                }
                return $description;
            }

            /** @return array<int,string> */
            public function getDynamicRoutes(): array
            {
                return array_keys($this->routes);
            }
        };
    }
}

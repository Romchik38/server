<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller;

use InvalidArgumentException;
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
use Romchik38\Server\Http\Controller\Dto\DynamicRouteDTO;
use Romchik38\Server\Http\Controller\Errors\ActionNotFoundException;
use Romchik38\Server\Http\Controller\Errors\CantCreateControllerChainException;
use Romchik38\Server\Http\Controller\Errors\DynamicActionLogicException;
use Romchik38\Server\Http\Controller\Errors\NoSuchControllerException;
use Romchik38\Server\Http\Controller\Middleware\RequestMiddlewareInterface;
use Romchik38\Server\Http\Controller\Middleware\ResponseMiddlewareInterface;

final class ControllerTest extends TestCase
{
    public function testIsPublicDefault(): void
    {
        $controller = new Controller('root');
        $this->assertSame(false, $controller->isPublic());
    }

    public function testIsPublicSet(): void
    {
        $controller = new Controller('root', true);
        $this->assertSame(true, $controller->isPublic());
    }

    public function testGetName(): void
    {
        $controller = new Controller('root');
        $this->assertSame('root', $controller->getName());
    }

    public function testGetDescriptionNoActions(): void
    {
        $controller = new Controller('root');
        $this->assertSame('root', $controller->getDescription());
    }

    public function testGetDescriptionDefaultAction(): void
    {
        $action     = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('hello world');
                return $response->withBody($body);
            }

            public function getDescription(): string
            {
                return 'Home';
            }
        };
        $controller = new Controller(
            'root',
            true,
            $action
        );

        $this->assertSame('Home', $controller->getDescription());
    }

    public function testGetDescriptionDynamicAction(): void
    {
        $action     = new class extends AbstractAction implements DynamicActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('<h1>About page</h1>');
                return $response->withBody($body);
            }

            public function getDescription(string $dynamicRoute): string
            {
                if ($dynamicRoute === 'about') {
                    return 'About Page';
                } else {
                    throw new DynamicActionLogicException('route not exist');
                }
            }

            public function getDynamicRoutes(): array
            {
                return [
                    new DynamicRouteDTO('about', 'Abou Page'),
                ];
            }
        };
        $controller = new Controller(
            'root',
            true,
            null,
            $action
        );

        $this->assertSame('About Page', $controller->getDescription('about'));
    }

    public function testGetDescriptionSetBoth(): void
    {
        $defaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('hello world');
                return $response->withBody($body);
            }

            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $dynamicAction = new class extends AbstractAction implements DynamicActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('<h1>About page</h1>');
                return $response->withBody($body);
            }

            public function getDescription(string $dynamicRoute): string
            {
                if ($dynamicRoute === 'about') {
                    return 'About Page';
                } else {
                    throw new DynamicActionLogicException('route not exist');
                }
            }

            public function getDynamicRoutes(): array
            {
                return [
                    new DynamicRouteDTO('about', 'Abou Page'),
                ];
            }
        };
        $controller    = new Controller(
            'root',
            true,
            $defaultAction,
            $dynamicAction
        );

        $this->assertSame('About Page', $controller->getDescription('about'));
    }

    public function testGetChildAndSetChild(): void
    {
        $root     = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );

        $root->setChild($products);
        $child = $root->getChild('products');

        $this->assertSame($products, $child);
    }

    public function testGetChildThrowsException(): void
    {
        $root     = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );

        $root->setChild($products);

        $this->expectException(NoSuchControllerException::class);
        $root->getChild('catalog');
    }

    public function testSetChildThrowsError(): void
    {
        $root     = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );

        $this->expectException(CantCreateControllerChainException::class);
        $products->setChild($root);
    }

    public function testGetChildren(): void
    {
        $root     = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );
        $reviews  = new Controller(
            'reviews'
        );
        $catalog  = new Controller(
            'catalog'
        );

        $this->assertSame([], $root->getChildren());
        $this->assertSame([], $products->getChildren());

        $root->setChild($products)->setChild($catalog);
        $products->setChild($reviews);

        $rootChildren = $root->getChildren();
        $this->assertSame($rootChildren['products'], $products);
        $this->assertSame($rootChildren['catalog'], $catalog);

        $productsChildren = $products->getChildren();
        $this->assertSame($productsChildren['reviews'], $reviews);
    }

    public function testGetCurrentParent(): void
    {
        $reviewsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('product reviews');
                return $response->withBody($body);
            }

            public function getDescription(): string
            {
                return 'Product Reviews';
            }
        };

        $root     = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );
        $reviews  = new Controller(
            'reviews',
            true,
            $reviewsDefaultAction
        );
        $catalog  = new Controller(
            'catalog'
        );

        $root->setChild($products)->setChild($catalog);
        $products->setChild($reviews);

        $elements = ['root', 'products', 'reviews'];
        $uri      = new Uri('http://example.com/products/reviews');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $root->handle($request);

        $this->assertSame($products, $reviews->getCurrentParent());
        $this->assertSame(null, $root->getCurrentParent());
    }

    public function testGetDynamicRoutes(): void
    {
        $rootDynamicAction = new class extends AbstractAction implements DynamicActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $route = $request->getAttribute(self::TYPE_DYNAMIC_ACTION);
                if ($route !== 'about') {
                    throw new ActionNotFoundException('Not found');
                }
                $response = new Response();
                $body     = $response->getBody();
                $body->write('Content of ' . $route);
                return $response->withBody($body);
            }

            public function getDescription(string $route): string
            {
                if ($route !== 'about') {
                    throw new DynamicActionLogicException('route not found');
                }
                return 'Description of about page';
            }

            public function getDynamicRoutes(): array
            {
                return [
                    new DynamicRouteDTO('about', 'About page'),
                ];
            }
        };

        $root = new Controller(
            'root',
            true,
            null,
            $rootDynamicAction
        );

        $dtos     = $root->getDynamicRoutes();
        $firstDto = $dtos[0];
        $this->assertSame('about', $firstDto->name());
        $this->assertSame('About page', $firstDto->description());
    }

    public function testGetFullPathDefaultRoute(): void
    {
        $reviewsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('product reviews');
                return $response->withBody($body);
            }

            public function getDescription(): string
            {
                return 'Product Reviews';
            }
        };

        $rootDynamicAction = new class extends AbstractAction implements DynamicActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $route = $request->getAttribute(self::TYPE_DYNAMIC_ACTION);
                if ($route !== 'about') {
                    throw new ActionNotFoundException('Not found');
                }
                $response = new Response();
                $body     = $response->getBody();
                $body->write('Content of ' . $route);
                return $response->withBody($body);
            }

            public function getDescription(string $route): string
            {
                if ($route !== 'about') {
                    throw new DynamicActionLogicException('route not found');
                }
                return 'Description of about page';
            }

            public function getDynamicRoutes(): array
            {
                return [
                    new DynamicRouteDTO('about', 'About page'),
                ];
            }
        };

        $root     = new Controller(
            'root',
            true,
            null,
            $rootDynamicAction
        );
        $products = new Controller(
            'products'
        );
        $reviews  = new Controller(
            'reviews',
            true,
            $reviewsDefaultAction
        );
        $catalog  = new Controller(
            'catalog'
        );

        $root->setChild($products)->setChild($catalog);
        $products->setChild($reviews);

        $elements = ['root', 'products', 'reviews'];
        $uri      = new Uri('http://example.com/products/reviews');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $root->handle($request);

        // $this->assertSame(['root', 'about'], $root->getFullPath('about'));
        $fullPath = $reviews->getFullPath();
        $this->assertSame(['root', 'products', 'reviews'], $fullPath);
    }

    public function testGetFullPathDynamicRoute()
    {
        $reviewsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('product reviews');
                return $response->withBody($body);
            }

            public function getDescription(): string
            {
                return 'Product Reviews';
            }
        };

        $rootDynamicAction = new class extends AbstractAction implements DynamicActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $route = $request->getAttribute(self::TYPE_DYNAMIC_ACTION);
                if ($route !== 'about') {
                    throw new ActionNotFoundException('Not found');
                }
                $response = new Response();
                $body     = $response->getBody();
                $body->write('Content of ' . $route);
                return $response->withBody($body);
            }

            public function getDescription(string $route): string
            {
                if ($route !== 'about') {
                    throw new DynamicActionLogicException('route not found');
                }
                return 'Description of about page';
            }

            public function getDynamicRoutes(): array
            {
                return [
                    new DynamicRouteDTO('about', 'About page'),
                ];
            }
        };

        $root     = new Controller(
            'root',
            true,
            null,
            $rootDynamicAction
        );
        $products = new Controller(
            'products'
        );
        $reviews  = new Controller(
            'reviews',
            true,
            $reviewsDefaultAction
        );
        $catalog  = new Controller(
            'catalog'
        );

        $root->setChild($products)->setChild($catalog);
        $products->setChild($reviews);

        $elements = ['root', 'products', 'reviews'];
        $uri      = new Uri('http://example.com/products/reviews');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $root->handle($request);
        $this->assertSame(['root', 'about'], $root->getFullPath('about'));
    }

    public function testGetParents(): void
    {
        $root     = new Controller('root');
        $products = new Controller('products');
        $help     = new Controller('help');
        $root->setChild($help)->setChild($products);
        $products->setChild($help);

        $parents = $help->getParents();
        $this->assertSame([$root, $products], $parents);
    }

    public function testSetCurrentParent(): void
    {
        $root     = new Controller('root');
        $products = new Controller('products');
        $help     = new Controller('help');
        $root->setChild($help)->setChild($products);
        $products->setChild($help);

        $help->setCurrentParent($root);
        $this->assertSame($root, $help->getCurrentParent());
    }

    public function testAddParent(): void
    {
        $root     = new Controller('root');
        $products = new Controller('products');
        $help     = new Controller('help');
        $help->addParent($root);
        $help->addParent($products);
        $this->assertSame([$root, $products], $help->getParents());
    }

    public function testAddRequestMiddleware(): void
    {
        $root       = new Controller('root');
        $middleware = new class implements RequestMiddlewareInterface
        {
            public function __invoke(ServerRequestInterface $request): ?ResponseInterface
            {
                return null;
            }
        };
        $root->addRequestMiddleware($middleware);
        $this->assertSame($middleware, $root->requestMiddlewares()[0]);
    }

    public function testAddResponseMiddleware(): void
    {
        $root       = new Controller('root');
        $middleware = new class implements ResponseMiddlewareInterface
        {
            public function __invoke(ResponseInterface $response): ResponseInterface
            {
                return $response;
            }
        };

        $root->addResponseMiddleware($middleware);
        $this->assertSame($middleware, $root->responseMiddlewares()[0]);
    }

    public function testPathCannotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Controller('');
    }

    public function testGetIdBasedOnName(): void
    {
        $root = new Controller('root');
        $this->assertSame('root', $root->getId());
    }

    public function testGetId(): void
    {
        $root = new Controller('root', true, null, null, 'some_id');
        $this->assertSame('some_id', $root->getId());
    }
}

<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Controllers\Errors\CantCreateControllerChain;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Controllers\Errors\NoSuchControllerException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;
use Romchik38\Server\Results\Controller\ControllerResultFactory;

class ControllerTest extends TestCase
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
        $action = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return 'hello world';
            }
            public function getDescription(): string
            {
                return 'Home';
            }
        };
        $controller = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            $action
        );

        $this->assertSame('Home', $controller->getDescription());
    }

    public function testGetDescriptionDynamicAction(): void
    {
        $action = new class extends Action implements DynamicActionInterface {
            public function execute(string $dynamicRoute): string
            {
                return '<h1>About page</h1>';
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
                    new DynamicRouteDTO('about', 'Abou Page')
                ];
            }
        };
        $controller = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            null,
            $action
        );

        $this->assertSame('About Page', $controller->getDescription('about'));
    }


    public function testGetDescriptionSetBoth(): void
    {
        $defaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return 'hello world';
            }
            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $dynamicAction = new class extends Action implements DynamicActionInterface {
            public function execute(string $dynamicRoute): string
            {
                return '<h1>About page</h1>';
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
                    new DynamicRouteDTO('about', 'Abou Page')
                ];
            }
        };
        $controller = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            $defaultAction,
            $dynamicAction
        );

        $this->assertSame('About Page', $controller->getDescription('about'));
    }

    public function testGetChildAndSetChild(): void
    {
        $root = new Controller(
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
        $root = new Controller(
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
        $root = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );

        $this->expectException(CantCreateControllerChain::class);
        $products->setChild($root);
    }

    public function testGetChildren(): void
    {
        $root = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );
        $reviews = new Controller(
            'reviews'
        );
        $catalog = new Controller(
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
        $reviewsDefaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return 'product reviews';
            }
            public function getDescription(): string
            {
                return 'Product Reviews';
            }
        };

        $root = new Controller(
            'root'
        );
        $products = new Controller(
            'products'
        );
        $reviews = new Controller(
            'reviews',
            true,
            new ControllerResultFactory,
            $reviewsDefaultAction
        );
        $catalog = new Controller(
            'catalog'
        );

        $root->setChild($products)->setChild($catalog);
        $products->setChild($reviews);

        $root->execute(['root', 'products', 'reviews']);

        $this->assertSame($products, $reviews->getCurrentParent());
    }

    
}

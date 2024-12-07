<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\ControllerLogicException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;
use Romchik38\Server\Results\Controller\ControllerResultFactory;

class ControllerResultFactoryTest extends TestCase
{
    public function testReturnResultFromDefault(): void
    {
        $rootDefaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return '<h1>Home page<h1>';
            }
            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $productsDefaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return '<h1>Products page<h1>';
            }
            public function getDescription(): string
            {
                return 'Products';
            }
        };

        $root = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            $rootDefaultAction
        );

        $products = new Controller(
            'products',
            true,
            new ControllerResultFactory,
            $productsDefaultAction
        );

        $root->setChild($products);

        $result = $root->execute(['root', 'products']);
        $this->assertSame('<h1>Products page<h1>', $result->getResponse());
    }


    public function testReturnResultFromDynamic(): void
    {
        $rootDefaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return '<h1>Home page<h1>';
            }
            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $productsDefaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return '<h1>Products page<h1>';
            }
            public function getDescription(): string
            {
                return 'Products';
            }
        };

        $rootDynamicAction = new class extends Action implements DynamicActionInterface {
            public function execute(string $route): string
            {
                if ($route !== 'about') throw new ActionNotFoundException('Not found');
                return 'Content about page';
            }
            public function getDescription(string $route): string
            {
                if ($route !== 'about') throw new DynamicActionLogicException('route not found');
                return 'Description of about page';
            }

            public function getDynamicRoutes(): array
            {
                return [
                    new DynamicRouteDTO('about', 'About page')
                ];
            }
        };

        $root = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            $rootDefaultAction,
            $rootDynamicAction
        );

        $products = new Controller(
            'products',
            true,
            new ControllerResultFactory,
            $productsDefaultAction
        );

        $root->setChild($products);

        $result = $root->execute(['root', 'about']);
        $this->assertSame('Content about page', $result->getResponse());
    }

    public function testResultFactoryWasNull(): void
    {
        $rootDefaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return '<h1>Home page<h1>';
            }
            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $rootDynamicAction = new class extends Action implements DynamicActionInterface {
            public function execute(string $route): string
            {
                if ($route !== 'about') throw new ActionNotFoundException('Not found');
                return 'Content about page';
            }
            public function getDescription(string $route): string
            {
                if ($route !== 'about') throw new DynamicActionLogicException('route not found');
                return 'Description of about page';
            }

            public function getDynamicRoutes(): array
            {
                return [
                    new DynamicRouteDTO('about', 'About page')
                ];
            }
        };

        $this->expectException(ControllerLogicException::class);
        new Controller(
            'root',
            true,
            null,
            $rootDefaultAction,
            $rootDynamicAction
        );
    }
}

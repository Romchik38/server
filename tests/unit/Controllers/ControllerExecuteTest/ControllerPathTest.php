<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Controllers\Errors\ControllerLogicException;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Results\Controller\ControllerResultFactory;

class ControllerPathTest extends TestCase
{
    public function testFindPath(): void
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

    public function testNotFindPath(): void
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

        $this->expectException(NotFoundException::class);
        $root->execute(['root', 'catalog']);
    }

    public function testEmptyElements(): void
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

        $this->expectException(ControllerLogicException::class);
        $root->execute([]);
    }
}

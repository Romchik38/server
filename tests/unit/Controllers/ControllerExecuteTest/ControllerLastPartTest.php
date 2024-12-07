<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;
use Romchik38\Server\Results\Controller\ControllerResultFactory;

class ControllerLastPartTest extends TestCase
{
    public function testExecuteDefaultAction(): void
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

        $root = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            $rootDefaultAction,
            $rootDynamicAction
        );

        $result = $root->execute(['root']);
        $this->assertSame('<h1>Home page<h1>', $result->getResponse());
    }

    public function testExecuteDynamicAction(): void
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

        $root = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            $rootDefaultAction,
            $rootDynamicAction
        );

        $result = $root->execute(['root', 'about']);
        $this->assertSame('Content about page', $result->getResponse());
    }
}

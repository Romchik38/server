<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;
use Romchik38\Server\Results\Controller\ControllerResultFactory;

class ControllerActionsThrowsError extends TestCase
{

    public function testDefaultActionThrowsNotFoundError(): void
    {
        $rootDefaultAction = new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                throw new ActionNotFoundException('not found, sorry');
            }

            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $root = new Controller(
            'root',
            true,
            new ControllerResultFactory,
            $rootDefaultAction
        );

        $this->expectException(NotFoundException::class);

        $root->execute(['root']);
    }

    public function testDynamicActionThrowsNotFoundError(): void
    {
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
            null,
            $rootDynamicAction
        );

        $this->expectException(NotFoundException::class);

        $root->execute(['root', 'contacts']);
    }
}

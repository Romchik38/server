<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\ControllerExecuteTest;

use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;
use Romchik38\Server\Http\Controller\Actions\DynamicActionInterface;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\Errors\ActionNotFoundException;
use Romchik38\Server\Http\Controller\Errors\DynamicActionLogicException;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

class ControllerActionsThrowsError extends TestCase
{
    public function testDefaultActionThrowsNotFoundError(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function execute(): ResponseInterface
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
            $rootDefaultAction
        );

        $this->expectException(NotFoundException::class);

        $root->execute(['root']);
    }

    public function testDynamicActionThrowsNotFoundError(): void
    {
        $rootDynamicAction = new class extends AbstractAction implements DynamicActionInterface {
            public function execute(string $route): ResponseInterface
            {
                if ($route !== 'about') {
                    throw new ActionNotFoundException('Not found');
                }
                $response = new Response();
                $body     = $response->getBody();
                $body->write('Content about page');
                $response = $response->withBody($body);
                return $response;
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

        $this->expectException(NotFoundException::class);

        $root->execute(['root', 'contacts']);
    }
}

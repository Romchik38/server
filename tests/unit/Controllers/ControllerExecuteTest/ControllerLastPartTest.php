<?php

declare(strict_types=1);

use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Controllers\Actions\AbstractAction;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Models\DTO\DynamicRoute\DynamicRouteDTO;

class ControllerLastPartTest extends TestCase
{
    public function testExecuteDefaultAction(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function execute(): ResponseInterface
            {
                $response = new Response();
                $body = $response->getBody();
                $body->write('<h1>Home page<h1>');
                $response = $response->withBody($body);
                return $response;
            }
            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $rootDynamicAction = new class extends AbstractAction implements DynamicActionInterface {
            public function execute(string $route): ResponseInterface
            {
                if ($route !== 'about') throw new ActionNotFoundException('Not found');
                $response = new Response();
                $body = $response->getBody();
                $body->write('Content about page');
                $response = $response->withBody($body);
                return $response;
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
            $rootDefaultAction,
            $rootDynamicAction
        );

        $result = $root->execute(['root']);
        $response = $result->getResponse();
        $this->assertSame('<h1>Home page<h1>', (string) $response->getBody());
    }

    public function testExecuteDynamicAction(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function execute(): ResponseInterface
            {
                $response = new Response();
                $body = $response->getBody();
                $body->write('<h1>Home page<h1>');
                $response = $response->withBody($body);
                return $response;
            }
            public function getDescription(): string
            {
                return 'Home';
            }
        };

        $rootDynamicAction = new class extends AbstractAction implements DynamicActionInterface {
            public function execute(string $route): ResponseInterface
            {
                if ($route !== 'about') throw new ActionNotFoundException('Not found');
                $response = new Response();
                $body = $response->getBody();
                $body->write('Content about page');
                $response = $response->withBody($body);
                return $response;
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
            $rootDefaultAction,
            $rootDynamicAction
        );

        $result = $root->execute(['root', 'about']);
        $response = $result->getResponse();
        $this->assertSame('Content about page', (string) $response->getBody());
    }
}

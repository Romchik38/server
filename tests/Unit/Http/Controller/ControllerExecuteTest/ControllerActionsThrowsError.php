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
use Romchik38\Server\Http\Controller\Dto\DynamicRouteDTO;
use Romchik38\Server\Http\Controller\Errors\ActionNotFoundException;
use Romchik38\Server\Http\Controller\Errors\DynamicActionLogicException;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;

class ControllerActionsThrowsError extends TestCase
{
    public function testDefaultActionThrowsNotFoundError(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
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

        $elements = ['root'];
        $uri      = new Uri('http://example.com/');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $root->handle($request);
    }

    public function testDynamicActionThrowsNotFoundError(): void
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

        $elements = ['root', 'contacts'];
        $uri      = new Uri('http://example.com/contacts');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $root->handle($request);
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Controllers;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\HtmlResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\Middleware\RequestMiddlewareInterface;

final class RequestMiddlewareTest extends TestCase
{
    public function testRequestMiddlewareReturnNull(): void
    {
        $rootDefaultAction = $this->createRootDefaultAction();

        $middleware = new class implements RequestMiddlewareInterface
        {
            public function __invoke(): ?ResponseInterface
            {
                return null;
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root->addRequestMiddleware($middleware);

        $response = $root->execute(['root']);
        $this->assertSame('<h1>Home page<h1>', (string) $response->getBody());
    }

    public function testRequestMiddlewareReturnResponse(): void
    {
        $rootDefaultAction = $this->createRootDefaultAction();

        $middleware = new class implements RequestMiddlewareInterface
        {
            public function __invoke(): ?ResponseInterface
            {
                return new HtmlResponse('from middleware');
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root->addRequestMiddleware($middleware);

        $response = $root->execute(['root']);
        $this->assertSame('from middleware', (string) $response->getBody());
    }

    public function testRequestMiddlewareReturnResponseWithAfewMiddlewares(): void
    {
        $rootDefaultAction = $this->createRootDefaultAction();

        $middleware1 = new class implements RequestMiddlewareInterface
        {
            public function __invoke(): ?ResponseInterface
            {
                return null;
            }
        };

        $middleware2 = new class implements RequestMiddlewareInterface
        {
            public function __invoke(): ?ResponseInterface
            {
                return new HtmlResponse('from middleware2');
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $root
            ->addRequestMiddleware($middleware1)
            ->addRequestMiddleware($middleware2);

        $response = $root->execute(['root']);
        $this->assertSame('from middleware2', (string) $response->getBody());
    }

    private function createRootDefaultAction(): DefaultActionInterface
    {
        return new class extends AbstractAction implements DefaultActionInterface {
            public function execute(): ResponseInterface
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
}

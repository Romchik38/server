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
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Errors\ControllerLogicException;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;

class ControllerPathTest extends TestCase
{
    public function testFindPath(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
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

        $productsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('<h1>Products page<h1>');
                $response = $response->withBody($body);
                return $response;
            }

            public function getDescription(): string
            {
                return 'Products';
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $products = new Controller(
            'products',
            true,
            $productsDefaultAction
        );

        $root->setChild($products);

        $elements = ['root', 'products'];
        $uri      = new Uri('http://example.com/products');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $response = $root->handle($request);
        $this->assertSame('<h1>Products page<h1>', (string) $response->getBody());
    }

    public function testNotFindPath(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
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

        $productsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('<h1>Products page<h1>');
                $response = $response->withBody($body);
                return $response;
            }

            public function getDescription(): string
            {
                return 'Products';
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $products = new Controller(
            'products',
            true,
            $productsDefaultAction
        );

        $root->setChild($products);

        $elements = ['root', 'contacatalogcts'];
        $uri      = new Uri('http://example.com/catalog');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $this->expectException(NotFoundException::class);
        $root->handle($request);
    }

    public function testEmptyElements(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
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

        $productsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('<h1>Products page<h1>');
                $response = $response->withBody($body);
                return $response;
            }

            public function getDescription(): string
            {
                return 'Products';
            }
        };

        $root = new Controller(
            'root',
            true,
            $rootDefaultAction
        );

        $products = new Controller(
            'products',
            true,
            $productsDefaultAction
        );

        $root->setChild($products);

        $this->expectException(ControllerLogicException::class);

        $elements = [];
        $uri      = new Uri('http://example.com/contacts');
        $request  = new ServerRequest([], [], $uri, 'GET')
        ->withAttribute(ControllerInterface::REQUEST_ELEMENTS_NAME, $elements);

        $root->handle($request);
    }
}

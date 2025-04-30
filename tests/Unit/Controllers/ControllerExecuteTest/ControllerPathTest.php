<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Controllers\ControllerExecuteTest;

use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;
use Romchik38\Server\Http\Controller\Controller;
use Romchik38\Server\Http\Controller\Errors\ControllerLogicException;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;

class ControllerPathTest extends TestCase
{
    public function testFindPath(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
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

        $productsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function execute(): ResponseInterface
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

        $response = $root->execute(['root', 'products']);
        $this->assertSame('<h1>Products page<h1>', (string) $response->getBody());
    }

    public function testNotFindPath(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
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

        $productsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function execute(): ResponseInterface
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

        $this->expectException(NotFoundException::class);
        $root->execute(['root', 'catalog']);
    }

    public function testEmptyElements(): void
    {
        $rootDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
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

        $productsDefaultAction = new class extends AbstractAction implements DefaultActionInterface {
            public function execute(): ResponseInterface
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
        $root->execute([]);
    }
}

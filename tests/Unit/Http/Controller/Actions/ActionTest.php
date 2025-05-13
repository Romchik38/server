<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Http\Controller\Actions;

use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\Actions\AbstractAction;
use Romchik38\Server\Http\Controller\Actions\ActionInterface;
use Romchik38\Server\Http\Controller\Actions\DefaultActionInterface;
use Romchik38\Server\Http\Controller\Controller;

class ActionTest extends TestCase
{
    public function testGetController(): void
    {
        $action = $this->createAction();

        $controller = new Controller(
            'root',
            true,
            $action
        );

        $this->assertSame($controller, $action->getController());
    }

    public function testSetController(): void
    {
        $action = $this->createAction();

        $controller = new Controller(
            'root',
            true
        );

        $action->setController($controller);

        $this->assertSame($controller, $action->getController());
    }

    protected function createAction(): ActionInterface
    {
        return new class extends AbstractAction implements DefaultActionInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $response = new Response();
                $body     = $response->getBody();
                $body->write('ok');
                return $response->withBody($body);
            }

            public function getDescription(): string
            {
                return 'des ok';
            }
        };
    }
}

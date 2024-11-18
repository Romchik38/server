<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Controllers\Actions\Action;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Results\Controller\ControllerResultFactory;

class ActionTest extends TestCase
{
    public function testGetController(): void
    {
        $action = $this->createAction();

        $controller = new Controller(
            'root',
            true,
            new ControllerResultFactory,
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
        return new class extends Action implements DefaultActionInterface {
            public function execute(): string
            {
                return 'ok';
            }
            public function getDescription(): string
            {
                return 'des ok';
            }
        };
    }
}

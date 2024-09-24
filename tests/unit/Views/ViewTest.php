<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Views\View;
use Romchik38\Server\Controllers\Controller;
use Romchik38\Server\Models\DTO\DefaultView\DefaultViewDTO;

class ViewTest extends TestCase
{
    /** test controller assign */
    public function testSetController()
    {
        $view = new class() extends View {
            public function toString(): string
            {
                return $this->controller->getName();
            }
        };

        $controllerName = 'some_name';
        $controller = new Controller($controllerName);


        $view->setController($controller);

        $this->assertSame($controllerName, $view->toString());
    }

    /** test action assign */
    public function testSetControllerWithoutAction()
    {
        $view = new class() extends View {
            public function toString(): string
            {
                return $this->action;
            }
        };

        $controllerName = 'some_name';
        $controller = new Controller($controllerName);


        $view->setController($controller);

        $this->assertSame('', $view->toString());
    }

    /** test action assign */
    public function testSetControllerWithAction()
    {
        $view = new class() extends View {
            public function toString(): string
            {
                return $this->action;
            }
        };

        $controllerName = 'some_name';
        $actionName = 'some_action';
        $controller = new Controller($controllerName);


        $view->setController($controller, $actionName);

        $this->assertSame($actionName, $view->toString());
    }

    public function testConrollerData()
    {
        $view = new class() extends View {
            public function toString(): string
            {
                return $this->controllerData->getName();
            }
        };

        $name = 'some name';
        $description = 'some description';
        $dto = new DefaultViewDTO($name, $description);

        $view->setControllerData($dto);

        $this->assertSame($name, $view->toString());
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Actions;

use Romchik38\Server\Http\Controller\ControllerInterface;

/**
 * Must be extended with DefaultActionInterface or DynamicActionInterface
 */
abstract class AbstractAction implements ActionInterface
{
    use RequestHandlerTrait;

    protected ControllerInterface $controller;

    public function getController(): ControllerInterface
    {
        return $this->controller;
    }

    public function setController(ControllerInterface $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * Used to identify a controller
     *
     * @return string[] Full path to controller with controller's name. Like ['root', 'about']
     * */
    protected function getPath(): array
    {
        $controller = $this->getController();
        return $controller->getFullPath();
    }
}

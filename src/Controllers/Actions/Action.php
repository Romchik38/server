<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Actions;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;

/** 
 * Must be extended with DefaultActionInterface or DynamicActionInterface
 */
abstract class Action implements ActionInterface
{

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
     * @return string[] Full path to controller with controller's name. Like ['root', 'about']
     * */
    protected function getPath(): array
    {
        $controller = $this->getController();
        return $controller->getFullPath();
    }
}

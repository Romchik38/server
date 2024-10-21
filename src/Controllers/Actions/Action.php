<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers\Actions;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;

/** 
 * Must be extended by DefaultAction or DynamicAction
 */
abstract class Action implements ActionInterface
{

    protected ControllerInterface $controller;

    public function getController(): ControllerInterface
    {
        return $this->controller;
    }

    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /** 
     * Use to identify a controller
     * 
     * @return string[] Full path to controller with controller's name. Like ['root', 'about']
     * */
    protected function getPath(): array
    {
        $controller = $this->getController();
        $name = $controller->getName();
        $paths = [];
        $stop = false;
        $current = $controller;
        while ($stop === false) {
            $stop = true;
            $parent = $current->getCurrentParent();
            if ($parent !== null) {
                $stop = false;
                $current = $parent;
                array_unshift($paths, $parent->getName());
            }
        }
        array_push($paths, $name);

        return $paths;
    }
}

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
}

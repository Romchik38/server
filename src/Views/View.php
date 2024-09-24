<?php

declare(strict_types=1);

namespace Romchik38\Server\Views;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;
use Romchik38\Server\Api\Views\ViewInterface;

abstract class View implements ViewInterface
{
    protected DefaultViewDTOInterface|null $controllerData = null;
    protected ControllerInterface|null $controller = null;
    protected string $action;

    /** @todo test this */
    public function setController(ControllerInterface $controller, string $action = ''): ViewInterface {
        $this->controller = $controller;
        $this->action = $action;
        return $this;        
    }

    /** @todo test this */
    public function setControllerData(DefaultViewDTOInterface $data): ViewInterface {
        $this->controllerData = $data;
        return $this;
    }

    abstract public function toString(): string;
}

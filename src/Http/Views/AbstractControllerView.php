<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Views\Dto\DefaultViewDTOInterface;

abstract class AbstractControllerView extends AbstractView implements ControllerViewInterface
{
    protected DefaultViewDTOInterface|null $controllerData = null;
    protected ControllerInterface|null $controller         = null;
    protected string $action                               = '';

    public function setController(
        ControllerInterface $controller,
        string $action = ''
    ): self {
        $this->controller = $controller;
        $this->action     = $action;
        return $this;
    }

    public function setControllerData(DefaultViewDTOInterface $data): self
    {
        $this->controllerData = $data;
        return $this;
    }
}

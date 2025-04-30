<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Actions;

use Romchik38\Server\Http\Controller\ControllerInterface;

interface ActionInterface
{
    public const TYPE_DEFAULT_ACTION = 'default_action';
    public const TYPE_DYNAMIC_ACTION = 'dynamic_action';

    public function getController(): ControllerInterface;

    /**
     * set corrent controller to the action
     *
     * @param ControllerInterface $controller - current controller
     */
    public function setController(ControllerInterface $controller): void;
}

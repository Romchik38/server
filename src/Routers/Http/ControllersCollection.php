<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Routers\Http\ControllersCollectionInterface;

/** 
 * this used only in the DynamicRootRouter
 */
class ControllersCollection implements ControllersCollectionInterface
{
    protected array $hash = [];

    public function getController(string $method): ControllerInterface|null
    {
        $controller = $this->hash[$method] ?? null;
        return $controller;
    }

    public function setController(ControllerInterface $controller, string $method): ControllersCollectionInterface
    {
        $this->hash[$method] = $controller;
        return $this;
    }

    public function getMethods(): array
    {
        return array_keys($this->hash);
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller;

use function array_keys;

/**
 * this used only in the DynamicRootRouter
 */
class ControllersCollection implements ControllersCollectionInterface
{
    /** @var array<string,ControllerInterface> $hash */
    protected array $hash = [];

    public function getController(string $method): ControllerInterface|null
    {
        return $this->hash[$method] ?? null;
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

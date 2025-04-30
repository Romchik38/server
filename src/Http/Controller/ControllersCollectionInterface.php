<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller;

/**
 * Creates a collection of key => value, where:
 *  - key is a HTTP METHOD like 'GET'
 *  - value is a root controller
 */
interface ControllersCollectionInterface
{
    public function getController(string $method): ControllerInterface|null;

    public function setController(ControllerInterface $controller, string $method): ControllersCollectionInterface;

    /** @return array<int,string> */
    public function getMethods(): array;
}

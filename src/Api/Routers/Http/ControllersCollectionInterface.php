<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Routers\Http;

use Romchik38\Server\Api\Controllers\ControllerInterface;

/** @todo refactor:
 *   - rename method to key
 *   - remove Http from namespace
 */
interface ControllersCollectionInterface
{
    public function getController(string $method): ControllerInterface|null;
    public function setController(ControllerInterface $controller, string $method): ControllersCollectionInterface;

    /** @return array<int,string> */
    public function getMethods(): array;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Views;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;
use Romchik38\Server\Views\Http\Errors\ViewBuildException;

interface ViewInterface
{
    public function setController(ControllerInterface $controller, string $action = ''): ViewInterface;

    public function setControllerData(DefaultViewDTOInterface $data): ViewInterface;

    /** 
     * @throws ViewBuildException
     */
    public function toString(): string;
}

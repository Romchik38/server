<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Views;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\DTOInterface;
use Romchik38\Site1\Api\Models\DTO\DefaultView\DefaultViewDTOInterface;

interface ViewInterface
{
    public function setController(ControllerInterface $controller, string $action = ''): ViewInterface;
    public function setControllerData(DefaultViewDTOInterface $data): ViewInterface;
    public function setMetadata(string $key, string $value): ViewInterface;
    public function toString(): string;
}

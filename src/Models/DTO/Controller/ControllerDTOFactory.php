<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Controller;

use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;

class ControllerDTOFactory implements ControllerDTOFactoryInterface
{
    public function create(
        string $name,
        array $path,
        array $children
    ): ControllerDTOInterface {
        return new ControllerDTO(
            $name,
            $path,
            $children
        );
    }
}

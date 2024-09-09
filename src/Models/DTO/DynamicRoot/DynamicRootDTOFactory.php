<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\DynamicRoot;

use Romchik38\Server\Api\Models\DTO\DynamicRoot\DynamicRootDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\DynamicRoot\DynamicRootDTOInterface;

class DynamicRootDTOFactory implements DynamicRootDTOFactoryInterface
{
    public function create(string $name): DynamicRootDTOInterface
    {
        return new DynamicRootDTO($name);
    }
}

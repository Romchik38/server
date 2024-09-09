<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\DymanicRoot;

use Romchik38\Server\Api\Models\DTO\DymanicRoot\DymanicRootDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\DymanicRoot\DymanicRootDTOInterface;

class DymanicRootDTOFactory implements DymanicRootDTOFactoryInterface
{
    public function create(string $name): DymanicRootDTOInterface
    {
        return new DymanicRootDTO($name);
    }
}

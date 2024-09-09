<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\DymanicRoot;

interface DymanicRootDTOFactoryInterface
{
    public function create(string $name): DymanicRootDTOInterface;
}

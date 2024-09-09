<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\DynamicRoot;

interface DynamicRootDTOFactoryInterface
{
    public function create(string $name): DynamicRootDTOInterface;
}

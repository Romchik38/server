<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

class EntityFactory implements EntityFactoryInterface
{
    public function create(): EntityModel
    {
        return new EntityModel();
    }
}

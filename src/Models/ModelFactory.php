<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

class ModelFactory implements ModelFactoryInterface
{
    public function create(): ModelInterface
    {
        return new Model();
    }
}

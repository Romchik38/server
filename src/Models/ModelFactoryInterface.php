<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

interface ModelFactoryInterface
{
    public function create(): ModelInterface;
}

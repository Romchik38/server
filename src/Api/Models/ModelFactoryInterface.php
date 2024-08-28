<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models;

use Romchik38\Server\Api\Models\ModelInterface;

interface ModelFactoryInterface {

    public function create(): ModelInterface;
}
<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

use Romchik38\Server\Api\Models\ModelFactoryInterface;
use Romchik38\Server\Api\Models\ModelInterface;
use Romchik38\Server\Models\Model;

class ModelFactory implements ModelFactoryInterface {

    public function create(): ModelInterface {
        return new Model();
    }

}
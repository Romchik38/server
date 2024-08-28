<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Entity;

use Romchik38\Server\Api\Models\Entity\EntityModelInterface;

interface EntityFactoryInterface {
    public function create(): EntityModelInterface;
}
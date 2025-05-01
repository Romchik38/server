<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

interface EntityFactoryInterface
{
    public function create(): EntityModelInterface;
}

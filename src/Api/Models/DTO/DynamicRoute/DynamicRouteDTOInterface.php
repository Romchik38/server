<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\DynamicRoute;

interface DynamicRouteDTOInterface
{
    /** Returns name to navigate through controller tree */
    public function name(): string;

    /** Returns readable name representation */
    public function description(): string;
}

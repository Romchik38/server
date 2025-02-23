<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Virtual;

use Romchik38\Server\Api\Models\ModelInterface;

interface VirtualRepositoryInterface
{
    /**
     * Create an empty entity
     */
    public function create(): ModelInterface;

    /**
     * Search entities by provided conditions ( expression and params )
     *
     * @param array<int,string> $params
     * @return ModelInterface[]
     */
    public function list(string $expression, array $params): array;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Virtual;

use Romchik38\Server\Api\Models\ModelInterface;

interface VirtualRepositoryInterface
{
    /**
     * Create an empty entity
     * 
     * @return ModelInterface
     */
    public function create(): ModelInterface;

    /** 
     * Search entities by provided conditions ( expression and params )
     * 
     * @return ModelInterface[]
     */
    public function list(string $expression, array $params): array;
}

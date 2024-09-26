<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\CompositeId;

use Romchik38\Server\Models\Errors\InvalidArgumentException;

interface CompositeIdDTOFactoryInterface
{
    /**
     * Create an Id DTO entity with provided array of values 
     * 
     * @param array $data [$key => $value, ...]
     * @throws InvalidArgumentException [if not enough keys are provided]
     * @return CompositeIdDTOInterface
     */
    public function create(
        array $data
    ): CompositeIdDTOInterface;
}

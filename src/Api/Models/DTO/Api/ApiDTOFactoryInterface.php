<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Api;

interface ApiDTOFactoryInterface
{
    public function create(
        string $name,
        string $description,
        string $status, 
        mixed $result
    ): ApiDTOInterface;
}

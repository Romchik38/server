<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Api;

use Romchik38\Server\Api\Models\DTO\Api\ApiDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Api\ApiDTOInterface;

class ApiDTOFactory implements ApiDTOFactoryInterface
{
    public function create(
        string $name,
        string $description,
        string $status,
        mixed $result
    ): ApiDTOInterface {
        return new ApiDTO(
            $name,
            $description,
            $status,
            $result
        );
    }
}

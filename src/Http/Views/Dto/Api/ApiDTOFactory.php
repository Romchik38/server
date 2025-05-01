<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Dto\Api;

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

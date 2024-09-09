<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\TranslateEntity;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;

class TranslateEntityDTOFactory implements TranslateEntityDTOFactoryInterface
{
    public function create(string $key, array $data): TranslateEntityDTOInterface
    {
        return new TranslateEntityDTO(
            $key,
            $data
        );
    }
}

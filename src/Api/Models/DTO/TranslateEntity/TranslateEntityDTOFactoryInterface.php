<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\TranslateEntity;

use Romchik38\Server\Services\Translate\TranslateEntityDTOInterface;

/** @deprecated */
interface TranslateEntityDTOFactoryInterface
{
    /**
     * Create translate dto entity
     *
     * @param array<string,string> $data [key => value, ...]
     */
    public function create(string $key, array $data): TranslateEntityDTOInterface;
}

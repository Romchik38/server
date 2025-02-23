<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;

interface TranslateStorageInterface
{
    /**
     * Get all dto entities by provided 2 languages
     *
     * @param string[] $languages [default_lang, current_lang]
     * @return array<string,TranslateEntityDTOInterface> a hash [key => DTO, ...]
     */
    public function getDataByLanguages(array $languages): array;

    /**
     * Get a dto entity by provided key
     *
     * @return array<string,TranslateEntityDTOInterface> A hash [key => DTO, ...]
     */
    public function getAllDataByKey(string $key): array;
}

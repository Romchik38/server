<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

interface TranslateStorageInterface
{

    /**
     * get all dto entities by provided 2 languages
     * 
     * @param string[] $languages [default_lang, current_lang]
     * @return TranslateEntityDTOInterface[] list of translate dto entities
     */
    public function getDataByLanguages(array $languages): array;

    /**
     * @throws NoSuchEntityException If entity doesn't exist
     * @return TranslateEntityDTOInterface
     */
    public function getAllDataByKey(string $key): TranslateEntityDTOInterface;
}

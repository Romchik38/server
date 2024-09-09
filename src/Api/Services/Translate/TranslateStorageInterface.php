<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;

interface TranslateStorageInterface
{

    /**
     * get all dto entities by provided 2 languages
     * 
     * @param string[] $languages [default_lang, current_lang]
     * @return TranslateEntityDTOInterface[] list of translate dto entities
     */
    public function getDataByLanguages(array $languages): array;
}

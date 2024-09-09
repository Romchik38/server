<?php

namespace Romchik38\Server\Api\Models\DTO\TranslateEntity;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

interface TranslateEntityDTOInterface extends DTOInterface
{
    /** returns translate key */
    public function getKey(): string;

    /** 
     * returns a text phrase by provided language
     *   or null otherwise
     */
    public function getPhrase(string $language): string|null;
}

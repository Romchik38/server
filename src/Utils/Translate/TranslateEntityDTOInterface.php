<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Translate;

interface TranslateEntityDTOInterface
{
    /** returns translate key */
    public function getKey(): string;

    /**
     * returns a text phrase by provided language
     *   or null otherwise
     */
    public function getPhrase(string $language): string|null;
}

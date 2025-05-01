<?php

declare(strict_types=1);

namespace Romchik38\Server\Utils\Translate;

class TranslateEntityDTO implements TranslateEntityDTOInterface
{
    /**
     * @param array<string,string> $phrases - [language => phrase, ...]
     */
    public function __construct(
        protected readonly string $key,
        protected array $phrases
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPhrase(string $language): string|null
    {
        return $this->phrases[$language] ?? null;
    }
}

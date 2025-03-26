<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Romchik38\Server\Services\Translate\TranslateException;

interface TranslateInterface
{
    /**
     * Returns 
     *  - translation in the current language 
     *  - or in the default language in case there is no translation in the current language
     *  - or the key, if no translate
     *
     * @param string $key - A Phrase to translate.
     * @throws TranslateException - Current language does not set.
     * @return string Translated string
     */
    public function t(string $key): string;

    /**
     * Translate a phrase by provided language
     *
     * @param string $key - A phrase to translate
     * @param string $language - A language
     * @return string - Translated string or key on fail
     */
    public function translate(string $key, string $language): string;
}

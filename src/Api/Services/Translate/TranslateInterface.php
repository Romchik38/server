<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Translate;

use Romchik38\Server\Services\Errors\TranslateException;

interface TranslateInterface
{
    /**
     * Translation in the current language or the default language in case
     *  there is no translation in the current language
     *
     * @param string $str - A Phrase to translate.
     * @throws TranslateException - A bad key or no translation for it in default language.
     * @return string Translated string
     */
    public function t(string $str): string;

    /**
     * Translate a phrase by provided language
     *
     * @param string $key A phrase to translate
     * @param string $language Translation language
     * @throws TranslateException A bad key or no translation for it in default language.
     * @return string Translated string
     */
    public function translate(string $key, string $language): string;
}

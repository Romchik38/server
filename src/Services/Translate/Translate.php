<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Translate\TranslateInterface;
use Romchik38\Server\Api\Services\Translate\TranslateStorageInterface;
use Romchik38\Server\Services\Errors\TranslateException;

/**
 * Translate a string by given key. Just pass the key 
 * like in the example below:
 *      $translate = new Translate($translateStorage, $DynamicRoot);
 *      echo $translate->t('login.index.h1');
 * 
 * Returns translated string for current language, 
 *   otherwise returns the string for default language
 *   otherwise throws an error (so the key must be in the translate storage)
 * 
 */
class Translate implements TranslateInterface
{
    protected string $defaultLang;
    protected string $currentLang;

    protected array|null $hash = null;

    public function __construct(
        protected readonly TranslateStorageInterface $translateStorage,
        protected readonly DynamicRootInterface $DynamicRoot
    ) {
        $this->defaultLang = $this->DynamicRoot->getDefaultRoot()->getName();
    }

    public function t(string $str): string
    {
        $this->currentLang = $this->currentLang ?? $this->DynamicRoot->getCurrentRoot()->getName();

        if ($this->hash === null) {
            $this->hash = $this->translateStorage->getDataByLanguages(
                [$this->defaultLang, $this->currentLang]
            );
        }

        $format = 'Translation for string %s is missing. Please create it for default %s language first';
        $formatDefaultVal = 'Default value for language %s isn\'t set';

        /** @var TranslateEntityDTOInterface $translateDTO*/
        $translateDTO = $this->hash[$str] ??
            /** you do not have a translate for given string at all */
            throw new TranslateException(sprintf($format, $str, $this->defaultLang));

        $defaultVal = $translateDTO->getPhrase($this->defaultLang) ??
            /** you do not have a translate for given string in default language */
            throw new TranslateException(sprintf($formatDefaultVal, $this->defaultLang));

            /** 
             * pass 
             * 
             * @todo add log with level debug if currentLang is null
             * */
        return $translateDTO->getPhrase($this->currentLang) ?? $defaultVal;
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;
use Romchik38\Server\Api\Services\DymanicRoot\DymanicRootInterface;
use Romchik38\Server\Api\Services\Translate\TranslateInterface;
use Romchik38\Server\Api\Services\Translate\TranslateStorageInterface;
use Romchik38\Server\Services\Errors\TranslateException;

/**
 * Translate a string by given key. Just pass the key 
 * like in the example below:
 *      $translate = new Translate($translateStorage, $dymanicRoot);
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
        protected readonly DymanicRootInterface $dymanicRoot
    ) {
        $this->defaultLang = $this->dymanicRoot->getDefaultRoot()->getName();
    }

    public function t(string $str): string
    {
        $this->currentLang = $this->currentLang ?? $this->dymanicRoot->getCurrentRoot()->getName();

        if ($this->hash === null) {
            $this->hash = $this->translateStorage->getDataByLanguages(
                [$this->defaultLang, $this->currentLang]
            );
        }

        /**
         * Want that translate works - load all phrases in the default language before change it
         */
        /** @var TranslateEntityDTOInterface $translateDTO*/
        $translateDTO = $this->hash[$str] ??
            throw new TranslateException('invalid trans string');
        $defaultVal = $translateDTO->getPhrase($this->defaultLang) ??
            throw new TranslateException('default value for lang ' . $this->defaultLang . ' isn\'t set');
        return $translateDTO->getPhrase($this->currentLang) ?? $defaultVal;
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Romchik38\Server\Api\Models\DTO\TranslateEntity\TranslateEntityDTOInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Translate\TranslateInterface;
use Romchik38\Server\Api\Services\Translate\TranslateStorageInterface;
use Romchik38\Server\Services\Errors\TranslateException;

use function count;
use function sprintf;

/**
 * Translate a string by given key. Just pass the key
 * like in the example below:
 *      $translate = new Translate($translateStorage, $DynamicRoot);
 *      echo $translate->t('login.index.h1');
 *
 * Returns translated string for current language,
 *   otherwise returns the string for default language
 *   otherwise throws an error (so the key must be in the translate storage)
 */
class Translate implements TranslateInterface
{
    protected string $defaultLang;
    protected string $currentLang;
    protected string $formatErrorMessage    =
        'Translation for string %s is missing. Please create it for default %s language first';
    protected string $formatErrorDefaultVal = 'Default value for language %s isn\'t set';

    /** @var array<string,TranslateEntityDTOInterface>|null $hash */
    protected array|null $hash = null;

    public function __construct(
        protected readonly TranslateStorageInterface $translateStorage,
        protected readonly DynamicRootInterface $dynamicRoot,
        protected readonly LoggerInterface|null $logger = null,
        protected readonly string $loglevel = LogLevel::DEBUG
    ) {
        $this->defaultLang = $this->dynamicRoot->getDefaultRoot()->getName();
    }

    public function t(string $str): string
    {
        $this->setCurrentLanguage();
        return $this->translate($str, $this->currentLang);
    }

    public function translate(string $key, string $language): string
    {
        $this->setCurrentLanguage();

        /** 1. Fill the hash */
        if ($this->hash === null) {
            $this->hash = $this->translateStorage->getDataByLanguages(
                [$this->defaultLang, $this->currentLang]
            );
        }

        /**
         * 3. Check if key exists
         * @var TranslateEntityDTOInterface $translateDto*/
        $translateDto = $this->hash[$key] ??
            /** you do not have a translate for given string at all */
            throw new TranslateException(sprintf($this->formatErrorMessage, $key, $this->defaultLang));

        /** 4. Check you do not have a translate for given string in default language */
        $defaultVal = $translateDto->getPhrase($this->defaultLang) ??
            throw new TranslateException(sprintf($this->formatErrorDefaultVal, $this->defaultLang));

        $translated = $translateDto->getPhrase($language);

        /** 5. return by provided language */
        if ($translated !== null) {
            return $translated;
        }

        /** 6. Check for specific language (get all translates for the given key)*/
        if ($language !== $this->defaultLang && $language !== $this->currentLang) {
            $array = $this->translateStorage->getAllDataByKey($key);
            if (count($array) === 1) {
                $dto              = $array[$key];
                $this->hash[$key] = $dto;

                $translated = $dto->getPhrase($language);

                if ($translated !== null) {
                    return $translated;
                }
            }
        }

        /**
         * 7. return by default language
         * */
        if ($this->logger !== null) {
            $this->logger->log(
                $this->loglevel,
                sprintf(
                    '%s: Missed translation for key %s language %s',
                    $this::class,
                    $key,
                    $language
                )
            );
        }
        return $defaultVal;
    }

    protected function setCurrentLanguage(): void
    {
        $this->currentLang = $this->currentLang ?? $this->dynamicRoot->getCurrentRoot()->getName();
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

use Psr\Log\LoggerInterface;

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
abstract class AbstractTranslate implements TranslateInterface
{
    protected string $defaultLang;

    public function __construct(
        protected readonly TranslateStorageInterface $translateStorage,
        protected readonly LoggerInterface|null $logger,
        protected readonly string $loglevel
    ) {
    }

    abstract public function t(string $key): string;

    public function translate(string $key, string $language): string
    {
        try {
            $translateDto = $this->translateStorage->getByKey($key);
        } catch (NoSuchTranslateException) {
            $this->doLog(sprintf(
                'Translate key %s does not exist',
                $key
            ));
            return $key;
        }

        $translated = $translateDto->getPhrase($language);
        if ($translated !== null) {
            return $translated;
        }

        $this->doLog(sprintf(
            'Translate key %s does not have translate in %s language',
            $key,
            $language
        ));

        $translated = $translateDto->getPhrase($this->defaultLang);
        if ($translated === null) {
            $this->doLog(sprintf(
                'Translate key %s does not have translate in %s default language',
                $key,
                $this->defaultLang
            ));
            return $key;
        }

        return $translated;
    }

    protected function doLog(string $message): void
    {
        if ($this->logger !== null) {
            $this->logger->log($this->loglevel, $message);
        }
    }
}

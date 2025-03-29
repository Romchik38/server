<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

interface TranslateStorageInterface
{
    /**
     * @throws NoSuchTranslateException
     * @throws TranslateStorageException - On any database errors.
     */
    public function getByKey(string $key): TranslateEntityDTOInterface;
}

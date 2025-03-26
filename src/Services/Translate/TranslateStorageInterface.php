<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Translate;

interface TranslateStorageInterface
{
    /**
     * @throws NoSuchTranslateException
     */
    public function getByKey(string $key): TranslateEntityDTOInterface;
}

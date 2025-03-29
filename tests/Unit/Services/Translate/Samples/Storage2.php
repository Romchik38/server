<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Translate\Samples;

use Romchik38\Server\Services\Translate\TranslateEntityDTOInterface;
use Romchik38\Server\Services\Translate\TranslateStorageException;
use Romchik38\Server\Services\Translate\TranslateStorageInterface;

final class Storage2 implements TranslateStorageInterface
{
    /** @param array<string,array<string,string>> $data */
    public function __construct(
        private readonly array $data
    ) {
    }

    public function getByKey(string $key): TranslateEntityDTOInterface
    {
        throw new TranslateStorageException('Database is down');
    }
}

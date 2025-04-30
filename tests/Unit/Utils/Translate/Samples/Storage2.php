<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Translate\Samples;

use Romchik38\Server\Utils\Translate\TranslateEntityDTOInterface;
use Romchik38\Server\Utils\Translate\TranslateStorageException;
use Romchik38\Server\Utils\Translate\TranslateStorageInterface;

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

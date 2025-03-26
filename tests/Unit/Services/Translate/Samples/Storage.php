<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Services\Translate\Samples;

use Romchik38\Server\Services\Translate\NoSuchTranslateException;
use Romchik38\Server\Services\Translate\TranslateEntityDTO;
use Romchik38\Server\Services\Translate\TranslateEntityDTOInterface;
use Romchik38\Server\Services\Translate\TranslateStorageInterface;

use function sprintf;

final class Storage implements TranslateStorageInterface
{
    /** @param array<string,array<string,string>> $data */
    public function __construct(
        private readonly array $data
    ) {
    }

    public function getByKey(string $key): TranslateEntityDTOInterface
    {
        $result = $this->data[$key] ?? null;
        if ($result === null) {
            throw new NoSuchTranslateException(sprintf(
                'Translate with key % does not exist',
                $key
            ));
        }

        return new TranslateEntityDTO($key, $result);
    }
}

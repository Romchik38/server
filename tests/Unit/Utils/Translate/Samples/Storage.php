<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Utils\Translate\Samples;

use Romchik38\Server\Utils\Translate\NoSuchTranslateException;
use Romchik38\Server\Utils\Translate\TranslateEntityDTO;
use Romchik38\Server\Utils\Translate\TranslateEntityDTOInterface;
use Romchik38\Server\Utils\Translate\TranslateStorageInterface;

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

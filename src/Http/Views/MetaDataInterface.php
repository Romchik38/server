<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Romchik38\Server\Http\Views\Errors\MetaDataException;

interface MetaDataInterface
{
    /**
     * @throws MetaDataException
     * @return array<string,mixed>
     * */
    public function getAll(): array;
}

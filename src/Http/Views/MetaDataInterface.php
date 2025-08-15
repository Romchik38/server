<?php

declare(strict_types=1);

use Romchik38\Server\Http\Views\Errors\MetadataException;

interface MetaDataInterface
{
    /**
     * @throws MetadataException
     * @return array<string,mixed>
     * */
    public function getAllData(): array;
}

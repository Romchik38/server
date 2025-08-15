<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use MetaDataInterface;

class MetaData implements MetaDataInterface
{
    /** @var array<string,mixed> $metaData */
    protected array $metaData = [];

    public function getAllData(): array
    {
        return $this->metaData;
    }

    protected function setMetadata(string $key, mixed $value): void
    {
        $this->metaData[$key] = $value;
    }
}

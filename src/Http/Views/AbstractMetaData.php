<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

abstract class AbstractMetaData implements MetaDataInterface
{
    /** @var array<string,mixed> $hash */
    protected array $hash = [];

    public function getAll(): array
    {
        $this->beforeGetAll();
        return $this->hash;
    }

    /** Replace with own implementation to fullfill $metaData in runtime */
    protected function beforeGetAll(): void
    {
    }
}

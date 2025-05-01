<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

/** @deprecated */
class DTO implements DTOInterface
{
    /** @var array<string,mixed> */
    protected array $data = [];

    public function getData(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function getAllData(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}

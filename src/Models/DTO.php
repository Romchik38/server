<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

class DTO implements DTOInterface
{
    protected array $data = [];

    public function getData(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function getAllData(): array
    {
        return $this->data;
    }
}

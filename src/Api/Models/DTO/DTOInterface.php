<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO;

use JsonSerializable;

interface DTOInterface extends JsonSerializable
{
    public function getData(string $key): mixed;
    public function getAllData(): array;
}

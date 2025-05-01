<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

use JsonSerializable;

/** @deprecated */
interface DTOInterface extends JsonSerializable
{
    public function getData(string $key): mixed;

    /** @return array<string,mixed> */
    public function getAllData(): array;
}

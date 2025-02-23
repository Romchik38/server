<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models;

use Romchik38\Server\Models\Errors\QueryExeption;

interface DatabaseInterface
{
    /**
     * @param array<int,int|string> $params
     * @throws QueryExeption
     * @return array<array<string,string>>
     * */
    public function queryParams(string $query, array $params): array;
}

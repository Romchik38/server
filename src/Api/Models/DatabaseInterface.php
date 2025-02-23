<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models;

use Romchik38\Server\Models\Errors\QueryException;

interface DatabaseInterface
{
    /**
     * @param array<int,int|string> $params
     * @throws QueryException
     * @return array<array<string,string>>
     * */
    public function queryParams(string $query, array $params): array;
}

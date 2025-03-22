<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models;

use Romchik38\Server\Models\Errors\QueryException;
use Romchik38\Server\Models\Sql\DatabaseTransactionException;

interface DatabaseInterface
{
    /**
     * @param array<int,int|string> $params
     * @throws QueryException
     * @return array<array<string,string>>
     * */
    public function queryParams(string $query, array $params): array;

    /**
     * @throws DatabaseTransactionException
     * */
    public function transactionStart(): void;

    /**
     * @throws DatabaseTransactionException
     * */
    public function transactionEnd(): void;

    /**
     * @throws DatabaseTransactionException
     * */
    public function transactionRollback(): void;

    /**
     * @param array<int,int|string> $params
     * @throws DatabaseTransactionException
     * @throws QueryException
     * @return array<array<string,string>>
     * */
    public function transactionQueryParams(string $query, array $params): array;
}

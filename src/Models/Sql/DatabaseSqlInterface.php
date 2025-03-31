<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql;

use Romchik38\Server\Models\Errors\DatabaseException;
use Romchik38\Server\Models\Errors\QueryException;
use Romchik38\Server\Models\Sql\DatabaseTransactionException;

interface DatabaseSqlInterface
{
    /** close connection */
    public function close(): void;

    /**
     * Must return
     *  - PGSQL_CONNECTION_OK
     *  - PGSQL_CONNECTION_BAD
     *
     * @throws DatabaseException - When connection was already closed.
     * */
    public function connectionStatus(): int;

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

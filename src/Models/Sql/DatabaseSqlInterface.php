<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql;

use Romchik38\Server\Models\Errors\DatabaseException;
use Romchik38\Server\Models\Errors\QueryException;
use Romchik38\Server\Models\Sql\DatabaseTransactionException;

interface DatabaseSqlInterface
{
    public const ISOLATION_LEVEL_READ_COMMITTED  = 'read committed';
    public const ISOLATION_LEVEL_REPEATABLE_READ = 'repeatable read';
    public const ISOLATION_LEVEL_SERIALIZABLE    = 'Serializable';

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
     * True  - connection is open
     * False - connection is closed
     */
    public function isConnected(): bool;

    /**
     * @param array<int,int|string> $params
     * @throws QueryException
     * @return array<array<string,string>>
     * */
    public function queryParams(string $query, array $params): array;

    /**
     * @throws DatabaseTransactionException
     * */
    public function transactionStart(
        string $level = self::ISOLATION_LEVEL_READ_COMMITTED
    ): void;

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

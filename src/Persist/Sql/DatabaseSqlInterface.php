<?php

declare(strict_types=1);

namespace Romchik38\Server\Persist\Sql;

interface DatabaseSqlInterface
{
    public const ISOLATION_LEVEL_READ_COMMITTED  = 'read committed';
    public const ISOLATION_LEVEL_REPEATABLE_READ = 'repeatable read';
    public const ISOLATION_LEVEL_SERIALIZABLE    = 'Serializable';

    /** close connection */
    public function close(): void;

    /**
     * True  - connection is open
     * False - connection is closed
     */
    public function isConnected(): bool;

    /**
     * @param array<int,int|string|null> $params
     * @throws QueryException
     * @return array<array<string,string|null>>
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
     * @param array<int,int|string|null> $params
     * @throws DatabaseTransactionException
     * @throws QueryException
     * @return array<array<string,string|null>>
     * */
    public function transactionQueryParams(string $query, array $params): array;
}

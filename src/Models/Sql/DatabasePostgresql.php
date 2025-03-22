<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql;

use PgSql\Connection;
use Romchik38\Server\Api\Models\DatabaseInterface;
use Romchik38\Server\Models\Errors\CreateConnectionException;
use Romchik38\Server\Models\Errors\DatabaseException;
use Romchik38\Server\Models\Errors\QueryException;

use function extension_loaded;
use function pg_close;
use function pg_connect;
use function pg_fetch_all;
use function pg_free_result;
use function pg_last_error;
use function pg_query;
use function pg_query_params;
use function pg_transaction_status;

use const PGSQL_TRANSACTION_IDLE;
use const PGSQL_TRANSACTION_INTRANS;

class DatabasePostgresql implements DatabaseInterface
{
    private Connection|null $connection = null;

    public function __construct(string $config)
    {
        if (extension_loaded('pgsql') === false) {
            throw new DatabaseException('Required extension: pgsql');
        }

        $connection = pg_connect($config);
        if ($connection === false) {
            throw new CreateConnectionException('Could\'t create connection');
        }
        $this->connection = $connection;
    }

    public function __destruct()
    {
        if ($this->connection !== null) {
            pg_close($this->connection);
        }
    }

    public function queryParams(string $query, array $params): array
    {
        if ($this->connection === null) {
            throw new CreateConnectionException('No connection to create a query');
        }
        $result = pg_query_params($this->connection, $query, $params);
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new QueryException($errMsg);
        }
        $arr = pg_fetch_all($result);
        pg_free_result($result);
        return $arr;
    }

    public function transactionStart(): void
    {
        if ($this->connection === null) {
            throw new DatabaseTransactionException('No connection to create a query');
        }

        $status = pg_transaction_status($this->connection);
        if ($status !== PGSQL_TRANSACTION_IDLE) {
            throw new DatabaseTransactionException('Transaction no idle');
        }

        $result = pg_query($this->connection, 'BEGIN');
        if ($result === false) {
            throw new DatabaseTransactionException('Could not start transaction');
        }
    }

    public function transactionEnd(): void
    {
        if ($this->connection === null) {
            throw new DatabaseTransactionException('No connection to create a query');
        }

        $status = pg_transaction_status($this->connection);
        if ($status !== PGSQL_TRANSACTION_INTRANS) {
            throw new DatabaseTransactionException('Transaction no idle in transaction block');
        }

        $result = pg_query($this->connection, 'COMMIT');
        if ($result === false) {
            throw new DatabaseTransactionException('Could not start transaction');
        }
    }

    public function transactionRollback(): void
    {
        if ($this->connection === null) {
            throw new DatabaseTransactionException('No connection to create a query');
        }

        $result = pg_query($this->connection, 'ROLLBACK');
        if ($result === false) {
            throw new DatabaseTransactionException('Could not rollback transaction');
        }
    }

    public function transactionQueryParams(string $query, array $params): array
    {
        if ($this->connection === null) {
            throw new DatabaseTransactionException('No connection to create a query');
        }

        $status = pg_transaction_status($this->connection);
        if ($status !== PGSQL_TRANSACTION_INTRANS) {
            throw new DatabaseTransactionException('Transaction no idle in transaction block');
        }

        $result = pg_query_params($this->connection, $query, $params);
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new QueryException($errMsg);
        }
        $arr = pg_fetch_all($result);
        pg_free_result($result);
        return $arr;
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql;

use PgSql\Connection;
use Romchik38\Server\Models\Errors\CreateConnectionException;
use Romchik38\Server\Models\Errors\DatabaseException;
use Romchik38\Server\Models\Errors\QueryException;
use Romchik38\Server\Models\Sql\DatabaseInterface;

use function extension_loaded;
use function ob_get_clean;
use function ob_start;
use function pg_close;
use function pg_connect;
use function pg_fetch_all;
use function pg_free_result;
use function pg_last_error;
use function pg_query;
use function pg_query_params;
use function pg_transaction_status;
use function sprintf;

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

        ob_start();
        $connection = pg_connect($config);
        $flushVar   = ob_get_clean();
        if ($connection === false) {
            throw new CreateConnectionException(sprintf(
                'Could not create connection: %s',
                $flushVar
            ));
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

        ob_start();
        $result   = pg_query_params($this->connection, $query, $params);
        $flushVar = ob_get_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new QueryException(sprintf('Query Error: %s', $errMsg));
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
        ob_start();
        $result   = pg_query($this->connection, 'BEGIN');
        $flushVar = ob_get_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new DatabaseTransactionException(sprintf(
                'Could not start transaction: %s',
                $errMsg
            ));
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
        ob_start();
        $result   = pg_query($this->connection, 'COMMIT');
        $flushVar = ob_get_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new DatabaseTransactionException(sprintf(
                'Could not end transaction: %s',
                $errMsg
            ));
        }
    }

    public function transactionRollback(): void
    {
        if ($this->connection === null) {
            throw new DatabaseTransactionException('No connection to create a query');
        }
        ob_start();
        $result   = pg_query($this->connection, 'ROLLBACK');
        $flushVar = ob_get_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new DatabaseTransactionException(sprintf(
                'Could not rollback transaction: %s',
                $errMsg
            ));
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

        ob_start();
        $result   = pg_query_params($this->connection, $query, $params);
        $flushVar = ob_get_clean();

        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new QueryException(sprintf(
                'Query error: %s',
                $errMsg
            ));
        }
        $arr = pg_fetch_all($result);
        pg_free_result($result);
        return $arr;
    }
}

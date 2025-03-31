<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql;

use PgSql\Connection;
use Romchik38\Server\Models\Errors\DatabaseException;
use Romchik38\Server\Models\Errors\QueryException;
use Romchik38\Server\Models\Sql\DatabaseSqlInterface;

use function extension_loaded;
use function ob_end_clean;
use function ob_get_clean;
use function ob_start;
use function pg_close;
use function pg_connect;
use function pg_connection_status;
use function pg_fetch_all;
use function pg_free_result;
use function pg_last_error;
use function pg_query;
use function pg_query_params;
use function pg_transaction_status;
use function sprintf;

use const PGSQL_TRANSACTION_IDLE;
use const PGSQL_TRANSACTION_INTRANS;

class DatabasePostgresql implements DatabaseSqlInterface
{
    private Connection $connection;
    private bool $isConnected;

    /**
     * @param int $flags
     *   0                              - last used connection (default)
     *   2 (PGSQL_CONNECT_FORCE_NEW)    - new
     *   4 (PGSQL_CONNECT_ASYNC)        - asynchronous connection
     * @throws DatabaseException - On missing pgsql extension and problem with a connection.
     * */
    public function __construct(string $config, int $flags = 0)
    {
        if (extension_loaded('pgsql') === false) {
            throw new DatabaseException('Required extension: pgsql');
        }

        ob_start();
        $connection = pg_connect($config, $flags);
        $flushVar   = ob_get_clean();
        if ($connection === false) {
            if ($flushVar === false) {
                $flushVar = '';
            }
            throw new DatabaseException(sprintf(
                'Could not create connection %s',
                $flushVar
            ));
        } else {
            $this->connection  = $connection;
            $this->isConnected = true;
        }
    }

    public function close(): void
    {
        if ($this->isConnected === true) {
            pg_close($this->connection);
            $this->isConnected = false;
        }
    }

    public function connectionStatus(): int
    {
        if ($this->isConnected === true) {
            return pg_connection_status($this->connection);
        } else {
            throw new DatabaseException('PostgreSQL connection has already been closed');
        }
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function queryParams(string $query, array $params): array
    {
        ob_start();
        $result = pg_query_params($this->connection, $query, $params);
        ob_end_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new QueryException(sprintf('Query Error: %s', $errMsg));
        }
        $arr = pg_fetch_all($result);
        pg_free_result($result);
        return $arr;
    }

    /** @todo test */
    public function transactionStart(
        string $level = self::ISOLATION_LEVEL_READ_COMMITTED
    ): void {
        $status = pg_transaction_status($this->connection);
        if ($status !== PGSQL_TRANSACTION_IDLE) {
            throw new DatabaseTransactionException('Transaction no idle');
        }
        ob_start();
        $result = pg_query($this->connection, sprintf(
            'BEGIN ISOLATION LEVEL %s',
            $level
        ));
        ob_end_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new DatabaseTransactionException(sprintf(
                'Could not start transaction: %s',
                $errMsg
            ));
        }
    }

    /** @todo test */
    public function transactionEnd(): void
    {
        $status = pg_transaction_status($this->connection);
        if ($status !== PGSQL_TRANSACTION_INTRANS) {
            throw new DatabaseTransactionException('Transaction no idle in transaction block');
        }
        ob_start();
        $result = pg_query($this->connection, 'COMMIT');
        ob_end_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new DatabaseTransactionException(sprintf(
                'Could not end transaction: %s',
                $errMsg
            ));
        }
    }

    /** @todo test */
    public function transactionRollback(): void
    {
        ob_start();
        $result = pg_query($this->connection, 'ROLLBACK');
        ob_end_clean();
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new DatabaseTransactionException(sprintf(
                'Could not rollback transaction: %s',
                $errMsg
            ));
        }
    }

    /** @todo test */
    public function transactionQueryParams(string $query, array $params): array
    {
        $status = pg_transaction_status($this->connection);
        if ($status !== PGSQL_TRANSACTION_INTRANS) {
            throw new DatabaseTransactionException('Transaction no idle in transaction block');
        }

        ob_start();
        $result = pg_query_params($this->connection, $query, $params);
        ob_end_clean();

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

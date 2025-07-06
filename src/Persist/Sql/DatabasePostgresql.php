<?php

declare(strict_types=1);

namespace Romchik38\Server\Persist\Sql;

use PgSql\Connection;
use RuntimeException;

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

use const PGSQL_CONNECTION_OK;
use const PGSQL_TRANSACTION_IDLE;
use const PGSQL_TRANSACTION_INTRANS;

/**
 * The Class represents a single database connection.
 * Connection is opened on the start and can be closed later.
 * It is not possible to reopen closed connection.
 */
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

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function queryParams(string $query, array $params): array
    {
        try {
            $this->checkConnectionIsOk();
        } catch (RuntimeException $e) {
            throw new QueryException(sprintf(
                '%s:%s',
                'Query is not possible',
                $e->getMessage()
            ));
        }

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

    public function transactionStart(
        string $level = self::ISOLATION_LEVEL_READ_COMMITTED
    ): void {
        try {
            $this->checkConnectionIsOk();
        } catch (RuntimeException $e) {
            throw new DatabaseTransactionException(sprintf(
                '%s.%s',
                $e->getMessage(),
                'Transaction start is not possible'
            ));
        }

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

    public function transactionEnd(): void
    {
        try {
            $this->checkConnectionIsOk();
        } catch (RuntimeException $e) {
            throw new DatabaseTransactionException(sprintf(
                '%s.%s',
                $e->getMessage(),
                'Transaction end is not possible'
            ));
        }

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

    public function transactionRollback(): void
    {
        try {
            $this->checkConnectionIsOk();
        } catch (RuntimeException $e) {
            throw new DatabaseTransactionException(sprintf(
                '%s.%s',
                $e->getMessage(),
                'Transaction rollback is not possible'
            ));
        }

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

    /** @throws RuntimeException */
    protected function checkConnectionIsOk(): void
    {
        if ($this->isConnected === false) {
            throw new RuntimeException('PostgreSQL connection has already been closed');
        } else {
            $status = pg_connection_status($this->connection);
            if ($status !== PGSQL_CONNECTION_OK) {
                throw new RuntimeException('PostgreSQL connection is bad');
            }
        }
    }
}

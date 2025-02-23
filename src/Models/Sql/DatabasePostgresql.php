<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql;

use PgSql\Connection;
use Romchik38\Server\Api\Models\DatabaseInterface;
use Romchik38\Server\Models\Errors\CreateConnectionExeption;
use Romchik38\Server\Models\Errors\DatabaseException;
use Romchik38\Server\Models\Errors\QueryExeption;

use function extension_loaded;
use function is_null;
use function pg_close;
use function pg_connect;
use function pg_fetch_all;
use function pg_free_result;
use function pg_last_error;
use function pg_query_params;

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
            throw new CreateConnectionExeption('Could\'t create connection');
        }
        $this->connection = $connection;
    }

    public function __destruct()
    {
        if (! is_null($this->connection)) {
            pg_close($this->connection);
        }
    }

    public function queryParams(string $query, array $params): array
    {
        if (is_null($this->connection)) {
            throw new CreateConnectionExeption('No connection to create a query');
        }
        $result = pg_query_params($this->connection, $query, $params);
        if ($result === false) {
            $errMsg = pg_last_error($this->connection);
            throw new QueryExeption($errMsg);
        }
        $arr = pg_fetch_all($result);
        pg_free_result($result);
        return $arr;
    }
}

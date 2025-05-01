<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Integration\Models\Sql;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Persist\Sql\DatabaseException;
use Romchik38\Server\Persist\Sql\DatabasePostgresql;
use Romchik38\Server\Persist\Sql\DatabaseTransactionException;
use Romchik38\Server\Persist\Sql\QueryException;
use RuntimeException;

use function pg_close;
use function pg_connect;
use function pg_query;
use function sprintf;

use const PGSQL_CONNECT_FORCE_NEW;
use const PGSQL_CONNECTION_OK;

final class DatabasePostgresqlTest extends TestCase
{
    private readonly string $connectionParams;

    protected function setUp(): void
    {
        $this->connectionParams = include __DIR__ . '/connection-params.php';
        $connection             = pg_connect($this->connectionParams);
        if ($connection === false) {
            $message = 'Looks like test postgresql database not works. Read docs/tests/database.md';
            throw new RuntimeException($message);
        } else {
            $products = include __DIR__ . '/Samples/product.php';
            pg_query($connection, 'DROP TABLE IF EXISTS products');
            pg_query($connection, 'CREATE TABLE products (id serial primary key, name text)');
            foreach ($products as $product) {
                pg_query($connection, sprintf(
                    'INSERT INTO products (id, name) values (\'%s\', \'%s\')',
                    $product[0],
                    $product[1]
                ));
            }
            pg_close($connection);
        }
    }

    public function testConnectionSuccess(): void
    {
        $database         = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $connectionStatus = $database->connectionStatus();
        $this->assertSame(PGSQL_CONNECTION_OK, $connectionStatus);
    }

    public function testConnectionThrowsError(): void
    {
        $this->expectException(DatabaseException::class);
        @new DatabasePostgresql('not exist');
    }

    public function testConnectionStatus(): void
    {
        $database         = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $connectionStatus = $database->connectionStatus();
        $this->assertSame(PGSQL_CONNECTION_OK, $connectionStatus);
        $database->close();
    }

    public function testConnectionStatusThrowsError(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $database->close();
        $this->expectException(DatabaseException::class);
        $database->connectionStatus();
        $database->close();
    }

    public function testIsConnected(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $this->assertSame(true, $database->isConnected());
        $database->close();
        $this->assertSame(false, $database->isConnected());
    }

    public function testQueryParams(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $query    = 'SELECT name from products WHERE id = $1';
        $params   = [1];
        $rows     = $database->queryParams($query, $params);
        $this->assertSame('Product 1', $rows[0]['name']);
    }

    public function testQueryParamsThrowsError(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $this->expectException(QueryException::class);
        @$database->queryParams('wrong query', []);
    }

    public function testQueryParamsThrowsErrorOnClosedConnection(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $database->close();
        $this->expectException(QueryException::class);
        @$database->queryParams('Select now()', []);
    }

    public function testTransactionStartEnd(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $database->transactionStart();
        $query  = 'SELECT name from products WHERE id = $1';
        $params = [1];
        $rows   = $database->transactionQueryParams($query, $params);
        $database->transactionEnd();
        $this->assertSame('Product 1', $rows[0]['name']);
        $database->close();
    }

    public function testTransactionStartThrowsErrorOnBusy(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $database->transactionStart();
        $this->expectException(DatabaseTransactionException::class);
        $database->transactionStart();
        $database->close();
    }

    public function testTransactionStartThrowsErrorOnWrongLevel(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $this->expectException(DatabaseTransactionException::class);
        @$database->transactionStart('wrong_level');
        $database->close();
    }

    public function testTransactionStartThrowsErrorOnClosedConnection()
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $database->close();
        $this->expectException(DatabaseTransactionException::class);
        $database->transactionStart();
    }

    public function testTransactionEndThrowsErrorOnNonBusy()
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $database->transactionStart();
        $database->transactionEnd();
        $this->expectException(DatabaseTransactionException::class);
        $database->transactionEnd();
        $database->close();
    }

    public function testTransactionEndThrowsErrorOnClosedConnection()
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $database->transactionStart();
        $database->close();
        $this->expectException(DatabaseTransactionException::class);
        $database->transactionEnd();
    }

    public function testTransactionRollback(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $database->transactionStart();
        $queryUpdate = 'UPDATE products SET name = \'some name\' WHERE id = $1';
        $querySelect = 'SELECT name from products WHERE id = $1';
        $params      = [1];
        $database->transactionQueryParams($queryUpdate, $params);
        $database->transactionRollback();
        $rows = $database->queryParams($querySelect, $params);
        $this->assertSame('Product 1', $rows[0]['name']);
        $database->close();
    }

    public function testTransactionRollbackThrowsErrorOnClosedConnection()
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $database->close();
        $this->expectException(DatabaseTransactionException::class);
        $database->transactionRollback();
    }

    public function testTransactionQueryParamsThrowsErrorOnClosedConnection(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $database->transactionStart();
        $database->close();
        $this->expectException(QueryException::class);
        @$database->transactionQueryParams('Select now()', []);
    }

    public function testTransactionQueryParamsThrowsErrorOnNonTransactionCall(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $this->expectException(DatabaseTransactionException::class);
        $database->transactionQueryParams('Select now()', []);
    }

    public function testTransactionQueryParams(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );

        $database->transactionStart();
        $queryUpdate = 'UPDATE products SET name = \'some name\' WHERE id = $1';
        $querySelect = 'SELECT name from products WHERE id = $1';
        $params      = [1];
        $database->transactionQueryParams($queryUpdate, $params);
        $database->transactionEnd();
        $rows = $database->queryParams($querySelect, $params);
        $this->assertSame('some name', $rows[0]['name']);
        $database->close();
    }

    public function testTransactionQueryParamsThrowsErrorOnWrongQuery(): void
    {
        $database = new DatabasePostgresql(
            $this->connectionParams,
            PGSQL_CONNECT_FORCE_NEW
        );
        $database->transactionStart();
        $this->expectException(QueryException::class);
        @$database->transactionQueryParams('Wrong query', []);
        $database->close();
    }
}

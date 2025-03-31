<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Integration\Models\Sql;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\Errors\DatabaseException;
use Romchik38\Server\Models\Sql\DatabasePostgresql;
use RuntimeException;

use function pg_close;
use function pg_connect;

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
}

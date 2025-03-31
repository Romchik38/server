<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Integration\Models\Sql;

use PHPUnit\Framework\TestCase;

final  class TestDatabasePostgresql extends TestCase
{
    private readonly string $connectionParams;

    protected function setUp(): void
    {
        $this->connectionParams = include_once __DIR__ . '/connection-params.php';
    }

    
}
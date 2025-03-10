<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\Sql\Virtual;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\Model;
use Romchik38\Server\Models\ModelFactory;
use Romchik38\Server\Models\Sql\DatabasePostgresql;
use Romchik38\Server\Models\Sql\Virtual\VirtualRepository;

use function count;
use function implode;

final class VirtualRepositoryTest extends TestCase
{
    private array $selectFields;
    private array $tables;

    public function setUp(): void
    {
        $this->selectFields = ['table1.*', 'table2.field1', 'table2.field2'];
        $this->tables       = ['table1', 'table2'];
    }

    /**
     * method create
     * tests:
     *   1 new instance creation
     */
    public function testCreate()
    {
        $database   = $this->createMock(DatabasePostgresql::class);
        $factory    = $this->createMock(ModelFactory::class);
        $repository = new VirtualRepository(
            $database,
            $factory,
            $this->selectFields,
            $this->tables
        );

        // prepare data
        $entity = new Model();

        $factory->expects($this->once())->method('create')->willReturn($entity);

        // exec
        $result = $repository->create();

        // 1 new instance creation
        $this->assertSame($entity, $result);
    }

    /**
     * method list
     * tests:
     *   1 query
     *   2 count of the entities
     *   3 entity data
     */
    public function testList()
    {
        $database   = $this->createMock(DatabasePostgresql::class);
        $factory    = $this->createMock(ModelFactory::class);
        $repository = new VirtualRepository(
            $database,
            $factory,
            $this->selectFields,
            $this->tables
        );

        // prepare data
        $expression    = 'WHERE model_key = $1';
        $params        = ['model_value'];
        $expectedQuery = 'SELECT ' . implode(', ', $this->selectFields)
            . ' FROM ' . implode(', ', $this->tables) . ' ' . $expression;
        $modelData     = [
            'model_key'  => 'model_value',
            'model_key2' => 'model_value2',
        ];
        $modelData2    = [
            'model2_key'  => 'model2_value',
            'model2_key2' => 'model2_value2',
        ];

        $factory->expects($this->exactly(2))->method('create')
            ->willReturn(new Model(), new Model());

        // 1 query and params
        $database->expects($this->once())->method('queryParams')
            ->willReturn([$modelData, $modelData2])
            ->with($this->callback(
                function ($query) use ($expectedQuery) {
                    if ($query !== $expectedQuery) {
                        return false;
                    }
                    return true;
                }
            ), ['model_value']);

        // exec
        $result = $repository->list($expression, $params);

        // 2 count of the entities
        $this->assertSame(2, count($result));

        // 3 entity data
        [$firstEntity, $secondEntity] = $result;
        $this->assertSame('model_value', $firstEntity->getData('model_key'));

        $this->assertSame('model2_value', $secondEntity->getData('model2_key'));
    }
}

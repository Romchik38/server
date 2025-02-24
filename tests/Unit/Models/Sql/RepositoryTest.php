<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\Sql;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\Errors\CouldNotAddException;
use Romchik38\Server\Models\Errors\CouldNotDeleteException;
use Romchik38\Server\Models\Errors\CouldNotSaveException;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Models\Errors\QueryException;
use Romchik38\Server\Models\Model;
use Romchik38\Server\Models\ModelFactory;
use Romchik38\Server\Models\Sql\DatabasePostgresql;
use Romchik38\Server\Models\Sql\Repository;

use function count;

final class RepositoryTest extends TestCase
{
    protected string $table            = 'table1';
    protected string $primaryFieldName = 'id';

    /**
     * method add
     * tests
     *   1 factory creation
     *   2 query
     *   3 entity
     *   4 entity data
     */
    public function testAdd()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $entity = new Model();
        $entity->setData('model_key1', 'model_value1');
        $entity->setData('model_key2', 'model_value2');
        $modelData     = ['model_key1' => 'model_value1', 'model_key2' => 'model_value2'];
        $expectedQuery = 'INSERT INTO ' . $this->table
            . ' (model_key1, model_key2) VALUES ($1, $2) RETURNING *';

        $entityFromFactory = new Model();
        // 1 factory creation
        $factory->expects($this->once())->method('create')->willReturn($entityFromFactory);

        // 2 query and params
        $database->expects($this->once())->method('queryParams')
            ->willReturn([$modelData])
            ->with($this->callback(
                function ($query) use ($expectedQuery) {
                    if ($query !== $expectedQuery) {
                        return false;
                    }
                    return true;
                }
            ), ['model_value1', 'model_value2']);

        $addedEntity = $repository->add($entity);

        // 3 entity
        $this->assertSame($entityFromFactory, $addedEntity);

        // 4 entity data
        $this->assertSame('model_value1', $addedEntity->getData('model_key1'));
    }

    /**
     * method add
     * throws CouldNotAddException
     */
    public function testAddThrowsError()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $database->method('queryParams')->willThrowException(new QueryException());

        $this->expectException(CouldNotAddException::class);

        $repository->add(new Model());
    }

    /**
     * method create
     * tests:
     *   1 factory creation
     *   2 entity
     */
    public function testCreate()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        // prepare data
        $entity = new Model();

        // 1 factory creation
        $factory->expects($this->once())->method('create')->willReturn($entity);

        $createdEntity = $repository->create();

        // 2 entity
        $this->assertSame($entity, $createdEntity);
    }

    /**
     * method deleteById
     * tests:
     *   1 query and params
     */
    public function testDeleteById()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $id            = 1;
        $expectedQuery = 'DELETE FROM ' . $this->table . ' WHERE '
            . $this->primaryFieldName . ' = $1';

        // 1 query and params
        $database->expects($this->once())->method('queryParams')
            ->with($this->callback(
                function ($query) use ($expectedQuery) {
                    if ($query !== $expectedQuery) {
                        return false;
                    }
                    return true;
                }
            ), [$id]);

        $repository->deleteById($id);
    }

    /**
     * metyhod deleteById
     * throws CouldNotDeleteException
     */
    public function testDeleteByIdThrowsError()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $database->method('queryParams')->willThrowException(new QueryException());

        $this->expectException(CouldNotDeleteException::class);
        $repository->deleteById(1);
    }

    /**
     * method getById
     * tests
     *   1 factory creation
     *   2 query and params
     *   3 entity
     *   4 entity data
     */
    public function testGetById()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $id                = 1;
        $entityFromFactory = new Model();
        $expectedQuery     = 'SELECT ' . $this->table . '.* FROM ' . $this->table
            . ' WHERE ' . $this->primaryFieldName . ' = $1';
        $modelData         = ['model_key1' => 'model_value1', 'model_key2' => 'model_value2'];

        // 1 factory creation
        $factory->expects($this->once())->method('create')->willReturn($entityFromFactory);

        // 2 query and params
        $database->expects($this->once())->method('queryParams')
            ->willReturn([$modelData])
            ->with($this->callback(
                function ($query) use ($expectedQuery) {
                    if ($query !== $expectedQuery) {
                        return false;
                    }
                    return true;
                }
            ), [$id]);

        $result = $repository->getById($id);

        // 3 entity
        $this->assertSame($entityFromFactory, $result);

        // 4 entity data
        $this->assertSame('model_value1', $result->getData('model_key1'));
    }

    /**
     * method getById
     * throws NoSuchEntityException
     */
    public function testGetByIdThrowsError()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $database->method('queryParams')->willReturn([]);

        $this->expectException(NoSuchEntityException::class);
        $repository->getById(1);
    }

    /**
     * method list
     * tests
     *   1 factory creation
     *   2 query and params
     *   3 count of the entities
     *   4 entity data
     */
    public function testList()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $expression    = ' WHERE id = $1';
        $expectedQuery = 'SELECT ' . $this->table . '.* FROM ' . $this->table . ' ' . $expression;
        $params        = ['model_value'];
        $modelData     = [
            'model_key'  => 'model_value',
            'model_key2' => 'model_value2',
        ];
        $modelData2    = [
            'model2_key'  => 'model2_value',
            'model2_key2' => 'model2_value2',
        ];

        // 1 factory creation
        $factory->expects($this->exactly(2))->method('create')
            ->willReturn(new Model(), new Model());

        // 2 query and params
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

        // 3 count of the entities
        $this->assertSame(2, count($result));

        // 4 entity data
        [$firstEntity, $secondEntity] = $result;
        $this->assertSame('model_value', $firstEntity->getData('model_key'));

        $this->assertSame('model2_value', $secondEntity->getData('model2_key'));
    }

    /**
     * method save
     * tests
     */
    public function testSave()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $id = 1;

        $entity = new Model();
        $entity->setData('model_key1', 'model_value1');
        $entity->setData('model_key2', 'model_value2');
        $entity->setData($this->primaryFieldName, $id);

        $expectedQuery = 'UPDATE ' . $this->table
            . ' SET model_key1 = $1, model_key2 = $2, '
            . $this->primaryFieldName . ' = $3 WHERE '
            . $this->primaryFieldName . ' = $4 RETURNING *';
        $modelData     = [
            'model_key1' => 'model_value1',
            'model_key2' => 'model_value2',
        ];

        $entityFromFactory = new Model();
        // 1 factory creation
        $factory->method('create')->willReturn($entityFromFactory);

        // 2 query and params
        $database->expects($this->once())->method('queryParams')
            ->willReturn([$modelData])
            ->with($this->callback(
                function ($query) use ($expectedQuery) {
                    if ($query !== $expectedQuery) {
                        return false;
                    }
                    return true;
                }
            ), ['model_value1', 'model_value2', $id, $id]);

        $result = $repository->save($entity);

        // 3 entity
        $this->assertSame($entityFromFactory, $result);

        // 4 entity data
        $this->assertSame('model_value1', $result->getData('model_key1'));
    }

    /**
     * method save
     * throws CouldNotSaveException
     */
    public function testSaveThrowsError()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(ModelFactory::class);

        $repository = new Repository(
            $database,
            $factory,
            $this->table,
            $this->primaryFieldName
        );

        $database->method('queryParams')->willThrowException(new QueryException());

        $this->expectException(CouldNotSaveException::class);
        $repository->save(new Model());
    }
}

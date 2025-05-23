<?php

declare(strict_types=1);

namespace Romchik38\Server\Tests\Unit\Models\Sql\Entity;

use PHPUnit\Framework\TestCase;
use Romchik38\Server\Models\EntityFactory;
use Romchik38\Server\Models\EntityModel;
use Romchik38\Server\Models\Errors\CouldNotAddException;
use Romchik38\Server\Models\Errors\CouldNotDeleteException;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Persist\Sql\DatabasePostgresql;
use Romchik38\Server\Persist\Sql\Entity\EntityRepository;
use Romchik38\Server\Persist\Sql\QueryException;

use function file_get_contents;

final class EntityRepositoryTest extends TestCase
{
    private string $entityTable            = 'entities';
    private string $fieldsTable            = 'entity_field';
    private string $primaryEntityFieldName = 'entity_id';
    private string $entityFieldName        = 'field_name';
    private string $entityValueName        = 'value';

    /**
     * Add new Entity
     * pass
     */
    public function testAdd()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $entity = new EntityModel();
        $entity->setEntityData('name', 'Test Entity for add method');
        $entity->email_contact_recovery = 'some@email';

        $fieldsRow = [
            [
                'field_name' => 'email_contact_recovery',
                'value'      => 'some@email',
            ],
        ];

        $entityRow = [
            [$this->primaryEntityFieldName => '1', 'name' => 'Test Entity for add method'],
        ];

        $factory->method('create')->willReturn(new EntityModel());

        $database->expects($this->exactly(2))->method('queryParams')
            ->willReturn($entityRow, $fieldsRow);

        $result = $repository->add($entity);

        $this->assertSame(
            $entityRow[0][$this->primaryEntityFieldName],
            $result->getEntityData($this->primaryEntityFieldName)
        );

        $this->assertSame(
            $fieldsRow[0][$this->entityValueName],
            $result->email_contact_recovery
        );
    }

    /**
     * Add new Entity
     * entity data is empty, so throw an error
     */
    public function testAddEntityDataEmptyThrowError()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $entity = new EntityModel();

        $this->expectException(CouldNotAddException::class);
        $repository->add($entity);
    }

    /**
     * Add new Entity
     * database throw an error
     */
    public function testAddDatabaseThrowError()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $entity = new EntityModel();
        $entity->setEntityData('name', 'Test Entity for add method');
        $entity->email_contact_recovery = 'some@email';

        $factory->method('create')->willReturn(new EntityModel());
        $database->method('queryParams')->willThrowException(new QueryException('some database error'));
        $this->expectException(CouldNotAddException::class);

        $repository->add($entity);
    }

    /**
     * Add fields to existing entity
     * pass
     */
    public function testAddFields()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $entity = new EntityModel();
        $entity->setEntityData($this->primaryEntityFieldName, 1);
        $entity->setEntityData('name', 'Test Entity for add method');

        $fields = [
            ['email_contact_recovery' => 'some@email'],
        ];

        $fieldsRow = [
            [
                'field_name' => 'email_contact_recovery',
                'value'      => 'some@email',
            ],
        ];

        $database->expects($this->once())->method('queryParams')
            ->willReturn($fieldsRow);

        $factory->method('create')->willReturn(new EntityModel());

        $result = $repository->addFields($fields, $entity);

        $this->assertSame('some@email', $result->email_contact_recovery);
    }

    /**
     * Testing method create
     */
    public function testCreate()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $entity = new EntityModel();
        $factory->method('create')->willReturn($entity);
        $this->assertSame($entity, $repository->create());
    }

    /**
     * deleteById method
     * throw error
     */
    public function testDeleteById()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $database->method('queryParams')->willThrowException(new QueryException('some database error'));
        $this->expectException(CouldNotDeleteException::class);
        $repository->deleteById(1);
    }

    /**
     * deleteFields method
     * pass
     * queries checks
     */
    public function testDeleteFields()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $entity = new EntityModel();
        $entity->setEntityData($this->primaryEntityFieldName, 1);
        $entity->email_contact_recovery = 'some@email';

        $fields = ['email_contact_recovery'];

        $deleteQuery       = 'DELETE FROM entity_field WHERE (field_name = $1) AND entity_id = $2';
        $selectEntityQuery = 'SELECT * FROM entities WHERE entity_id = $1';
        $selectFieldQuery  = 'SELECT * FROM entity_field WHERE entity_id = $1';

        $factory->method('create')->willReturn(new EntityModel());

        $fieldsRow = [];

        $entityRow = [
            [$this->primaryEntityFieldName => '1'],
        ];

        $database->expects($this->exactly(3))->method('queryParams')
            ->willReturn([], $entityRow, $fieldsRow)
            ->with($this->callback(
                function ($param) use ($deleteQuery, $selectEntityQuery, $selectFieldQuery) {
                    if (
                        $param === $deleteQuery ||
                        $param === $selectEntityQuery ||
                        $param === $selectFieldQuery
                    ) {
                        return true;
                    }
                    return false;
                }
            ));

        $result = $repository->deleteFields($fields, $entity);

        $deletedField = $result->email_contact_recovery;
        $this->assertSame(null, $deletedField);
    }

        /**
         * getById with Existing Id
         */
    public function testGetById()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $id              = 1;
        $fieldNameEmail  = 'email_contact_recovery';
        $fieldValueEmail = 'some@mail.com';

        $fieldsRow = [
            [
                'field_name' => $fieldNameEmail,
                'entity_id'  => '1',
                'value'      => $fieldValueEmail,
            ],
            [
                'field_name' => 'min_order_sum',
                'entity_id'  => '1',
                'value'      => '100',
            ],
        ];

        $entityRow = [
            [$this->primaryEntityFieldName => '1', 'name' => 'Test Entity for getById method'],
        ];

        $database->expects($this->exactly(2))->method('queryParams')
            ->willReturn($entityRow, $fieldsRow);

        $entity = new EntityModel();
        $factory->method('create')->willReturn($entity);

        $result = $repository->getById($id);

        $this->assertSame($fieldValueEmail, $result->email_contact_recovery);
    }

    /**
     * getById with not existing Id
     */
    public function testGetByIdNotFound()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $id = 1;

        $database->expects($this->once())->method('queryParams')
            ->willReturn([]);

        $this->expectException(NoSuchEntityException::class);

        $repository->getById($id);
    }

    // listByEntities
    // listByFields
    // save

    /**
     * listByEntities
     * pass
     * query checks
     */
    public function testListByEntities()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $expression  = 'WHERE ' . $this->primaryEntityFieldName . ' = $1';
        $params      = [1];
        $listQuery   = 'SELECT entities.* FROM entities WHERE entity_id = $1';
        $fieldsQuery = 'SELECT entity_field.* FROM entity_field WHERE entity_id = 1';

        $factory->method('create')->willReturn(new EntityModel());

        $fieldsRow = [
            [
                $this->entityFieldName => 'email_contact_recovery',
                $this->entityValueName => 'some@email',
            ],
        ];

        $entityRow = [
            [$this->primaryEntityFieldName => '1'],
        ];

        $database->method('queryParams')->willReturn($entityRow, $fieldsRow)
            ->with($this->callback(
                function ($param) use ($listQuery, $fieldsQuery) {
                    if (
                        $param === $listQuery ||
                        $param === $fieldsQuery
                    ) {
                        return true;
                    }
                    return false;
                }
            ));

        $entities = $repository->listByEntities($expression, $params);
        $entity   = $entities[0];

        $this->assertSame('1', $entity->getEntityData($this->primaryEntityFieldName));
        $this->assertSame('some@email', $entity->email_contact_recovery);
    }

       /**
        * listByFields
        * pass
        * query checks
        */
    public function testListByFields()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $expression = 'WHERE ' . $this->entityFieldName . ' = $1';
        $params     = ['email_contact_recovery'];

        $listQuery   = file_get_contents(__DIR__ . '/select1.sql');
        $fieldsQuery = 'SELECT entity_field.* FROM entity_field WHERE entity_id = 1';

        $factory->method('create')->willReturn(new EntityModel());

        $fieldsRow = [
            [
                $this->entityFieldName => 'email_contact_recovery',
                $this->entityValueName => 'some@email',
            ],
        ];

        $entityRow = [
            [$this->primaryEntityFieldName => '1'],
        ];

        $database->method('queryParams')->willReturn($entityRow, $fieldsRow)
            ->with($this->callback(
                function ($param) use ($listQuery, $fieldsQuery) {
                    if (
                        $param === $listQuery ||
                        $param === $fieldsQuery
                    ) {
                        return true;
                    }
                    return false;
                }
            ));

        $entities = $repository->listByFields($expression, $params);
        $entity   = $entities[0];

        $this->assertSame('1', $entity->getEntityData($this->primaryEntityFieldName));
        $this->assertSame('some@email', $entity->email_contact_recovery);
    }

    /**
     * save method for existing entity
     * pass
     * query check
     */
    public function testSave()
    {
        $database = $this->createMock(DatabasePostgresql::class);
        $factory  = $this->createMock(EntityFactory::class);

        $repository = new EntityRepository(
            $database,
            $factory,
            $this->entityTable,
            $this->fieldsTable,
            $this->primaryEntityFieldName,
            $this->entityFieldName,
            $this->entityValueName
        );

        $entity = new EntityModel();
        $entity->setEntityData($this->primaryEntityFieldName, 1);
        $entity->setEntityData('name', 'Some name for Save method');
        $entity->email_contact_recovery = 'some@email';

        $updateEntityQuery = 'UPDATE entities SET name = $1 WHERE entities.entity_id = $2 RETURNING *';
        $updateFieldsQuery = file_get_contents(__DIR__ . '/update1.sql');
        $selectQuery       = 'SELECT entity_field.* FROM entity_field WHERE entity_id = 1';

        $factory->method('create')->willReturn(new EntityModel());

        $entityRow = [
            [$this->primaryEntityFieldName => '1', 'name' => 'Some name for Save method'],
        ];

        $fieldsRow = [
            [
                $this->entityFieldName => 'email_contact_recovery',
                $this->entityValueName => 'some@email',
            ],
        ];

        $database->method('queryParams')->willReturn($entityRow, $fieldsRow)
        ->with($this->callback(
            function ($param) use ($updateEntityQuery, $updateFieldsQuery, $selectQuery) {
                if (
                    $param === $updateEntityQuery ||
                    $param === $updateFieldsQuery ||
                    $param === $selectQuery
                ) {
                    return true;
                }
                return false;
            }
        ));

        $savedEntity = $repository->save($entity);

        $this->assertSame('1', $savedEntity->getEntityData($this->primaryEntityFieldName));
        $this->assertSame('some@email', $savedEntity->email_contact_recovery);
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql\Entity;

use Romchik38\Server\Api\Models\Entity\EntityFactoryInterface;
use Romchik38\Server\Api\Models\Entity\EntityModelInterface;
use Romchik38\Server\Api\Models\Entity\EntityRepositoryInterface;
use Romchik38\Server\Models\Errors\CouldNotAddException;
use Romchik38\Server\Models\Errors\CouldNotDeleteException;
use Romchik38\Server\Models\Errors\CouldNotSaveException;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Models\Errors\QueryException;
use Romchik38\Server\Models\Sql\DatabaseSqlInterface;

use function count;
use function implode;

class EntityRepository implements EntityRepositoryInterface
{
    public function __construct(
        protected DatabaseSqlInterface $database,
        protected EntityFactoryInterface $entityFactory,
        protected string $entityTable,
        protected string $fieldsTable,
        protected string $primaryEntityFieldName,
        protected string $entityFieldName,
        protected string $entityValueName
    ) {
    }

    /**
     * Saves in a database new entity with fields
     *
     * @param EntityModelInterface $model [new entity without id]
     * @throws CouldNotAddException
     * @return EntityModelInterface [fresh entity copy already with id]
     */
    public function add(EntityModelInterface $model): EntityModelInterface
    {
        $allEntityData = $model->getAllEntityData();
        if (count($allEntityData) === 0) {
            // no entity data specified, so throw error
            throw new CouldNotAddException('No entity data specified');
        }
        // 1 add entity data
        $keys   = [];
        $values = [];
        $params = [];
        $count  = 0;

        foreach ($model->getAllEntityData() as $key => $value) {
            $count++;
            $params[] = '$' . $count;
            $keys[]   = $key;
            $values[] = $value;
        }

        $query = 'INSERT INTO ' . $this->entityTable . ' (' . implode(', ', $keys) . ') VALUES ('
            . implode(', ', $params) . ') RETURNING *';
        try {
            $arr       = $this->database->queryParams($query, $values);
            $entityRow = $arr[0];

            // 2 add fields data
            $entityId = (int) $entityRow[$this->primaryEntityFieldName];
            $fields   = $model->getFieldsData();

            // @throws CouldNotAddException
            $fieldsRow = $this->insertFields($fields, $entityId);
            return $this->createFromRow($entityRow, $fieldsRow);
        } catch (QueryException $e) {
            throw new CouldNotAddException($e->getMessage());
        }
    }

    public function addFields(
        array $fields,
        EntityModelInterface $entity
    ): EntityModelInterface {
        $entityRow = $entity->getAllEntityData();
        $entityId  = $entityRow[$this->primaryEntityFieldName];
        $fieldsRow = $this->insertFields($fields, $entityId);
        return $this->createFromRow($entityRow, $fieldsRow);
    }

    /**
     * create an empty entity
     */
    public function create(): EntityModelInterface
    {
        return $this->entityFactory->create();
    }

    /**
     * Delete an entity from database.
     * Fields from $this->fieldsTable will be deleteted auto via sql ON DELETE CASCADE
     *
     * @param int $id [an entity id]
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id): void
    {
        $query = 'DELETE FROM ' . $this->entityTable . ' WHERE '
        . $this->primaryEntityFieldName . ' = $1';
        try {
            $this->database->queryParams($query, [$id]);
        } catch (QueryException $e) {
            throw new CouldNotDeleteException($e->getMessage());
        }
    }

    public function deleteFields(
        array $fields,
        EntityModelInterface $entity
    ): EntityModelInterface {
        $count  = 0;
        $values = [];
        $params = [];
        foreach ($fields as $field) {
            ++$count;
            $values[] = $this->entityFieldName . ' = $' . $count;
            $params[] = $field;
        }

        $entityId = $entity->getEntityData($this->primaryEntityFieldName);
        $params[] = $entityId;

        $query = 'DELETE FROM ' . $this->fieldsTable . ' WHERE (' . implode(" OR ", $values)
        . ') AND ' . $this->primaryEntityFieldName . ' = $' . ++$count;

        try {
            $this->database->queryParams($query, $params);
            return $this->getById($entityId);
        } catch (QueryException $e) {
            throw new CouldNotDeleteException($e->getMessage());
        }
    }

    /**
     * find an entity by provided id
     *
     * @throws NoSuchEntityException
     */
    public function getById(int $id): EntityModelInterface
    {
        // 1. find an entity
        $query  = 'SELECT * FROM '
            . $this->entityTable
            . ' WHERE ' . $this->primaryEntityFieldName . ' = $1';
        $params = [$id];
        $arr    = $this->database->queryParams($query, $params);
        if (count($arr) === 0) {
            throw new NoSuchEntityException('row with id ' . $id
                . ' do not present in the ' . $this->primaryEntityFieldName . ' table');
        }
        $entityRow = $arr[0];
        // 2. select all fields
        $queryFields  = 'SELECT * FROM '
        . $this->fieldsTable
        . ' WHERE ' . $this->primaryEntityFieldName . ' = $1';
        $paramsFields = [$id];
        $fieldsRow    = $this->database->queryParams($queryFields, $paramsFields);

        return $this->createFromRow($entityRow, $fieldsRow);
    }

    public function listByEntities(string $expression, array $params): array
    {
        // 1 select entities
        $query = 'SELECT ' . $this->entityTable . '.* FROM ' . $this->entityTable . ' ' . $expression;
        $arr   = $this->database->queryParams($query, $params);

        // 2. select fields
        return $this->selectFields($arr);
    }

    public function listByFields(string $expression, array $params): array
    {
        // select distinct entity_id from entity_field where field_name like '%defa%';

        // 1. select entities
        $query = 'SELECT ' . $this->entityTable . '.* FROM ' . $this->entityTable
            . ' WHERE ' . $this->primaryEntityFieldName
            . ' IN (SELECT DISTINCT ' . $this->fieldsTable . '.' . $this->primaryEntityFieldName
            . ' FROM ' . $this->fieldsTable . ' ' . $expression . ')';
        $arr   = $this->database->queryParams($query, $params);

        // 2. select fields
        return $this->selectFields($arr);
    }

    /**
     * Save existing entity. Use add method if you want to save a new one
     *
     * @return EntityModelInterface [a fresh copy of given entity]
     */
    public function save(EntityModelInterface $model): EntityModelInterface
    {
        // 1 save an entity
        $params = [];
        $fields = [];
        $count  = 0;
        foreach ($model->getAllEntityData() as $key => $value) {
            if ($key === $this->primaryEntityFieldName) {
                continue;
            }
            $count++;
            $param    = '$' . $count;
            $params[] = $value;
            $fields[] = $key . ' = ' . $param;
        }

        $query = 'UPDATE ' . $this->entityTable . ' SET ' . implode(', ', $fields)
            . ' WHERE ' . $this->entityTable . '.' . $this->primaryEntityFieldName
            . ' = $' . ++$count . ' RETURNING *';

        $params[] = $this->primaryEntityFieldName;

        try {
            if (count($fields) > 0) {
                $arr       = $this->database->queryParams($query, $params);
                $entityRow = $arr[0];
            } else {
                // only entity_id was provided with entity data, so no query needed
                $entityRow = $model->getAllEntityData();
            }
            // 2 save entity fields
            $params2 = [];
            $fields2 = [];
            $count2  = 0;
            foreach ($model->getFieldsData() as $key2 => $value2) {
                $count2++;
                $param2    = '$' . $count2;
                $params2[] = $value2;
                $fields2[] = $key2 . ' = ' . $param2;
            }

            $query2 = 'UPDATE ' . $this->fieldsTable . ' SET ' . implode(', ', $fields2)
            . ' WHERE ' . $this->fieldsTable . '.' . $this->primaryEntityFieldName
            . ' = $' . ++$count2 . ' RETURNING *';

            $params2[] = $this->primaryEntityFieldName;

            try {
                $fieldsRow = $this->database->queryParams($query2, $params2);
            } catch (QueryException $e) {
                throw new CouldNotSaveException($e->getMessage());
            }
            // 3 return saved entity
            return $this->createFromRow($entityRow, $fieldsRow);
        } catch (QueryException $e) {
            throw new CouldNotSaveException($e->getMessage());
        }
    }

    /**
     * create an entity from row
     *
     * @param array<string,string> $entityRow
     * @param array<int,array<string,string>> $fieldsRow
     */
    protected function createFromRow(
        array $entityRow,
        array $fieldsRow
    ): EntityModelInterface {
        $entity = $this->entityFactory->create();
        foreach ($entityRow as $key => $value) {
            $entity->setEntityData($key, $value);
        }

        foreach ($fieldsRow as $field) {
            $key          = $field[$this->entityFieldName];
            $value        = $field[$this->entityValueName];
            $entity->$key = $value;
        }

        return $entity;
    }

    /**
     * insert rows into fields table by provided id
     *
     * @param array<string,string> $fields [field_name => field_value, ...]
     * @throws CouldNotAddException
     * @return array<array<string,string>>
     */
    protected function insertFields(array $fields, int $entityId): array
    {
        $values = [];
        $params = [];
        $count  = 0;

        foreach ($fields as $key => $value) {
            $fullValue = '(';
            // field
            $count++;
            $params[]   = $key;
            $fullValue .= '$' . $count;
            // value
            $count++;
            $params[]   = $value;
            $fullValue .= ', $' . $count;
            // entity id
            $count++;
            $params[]   = $entityId;
            $fullValue .= ', $' . $count . ')';
            // finish
            $values[] = $fullValue;
        }

        $query = 'INSERT INTO ' . $this->fieldsTable
            . ' (' . $this->entityFieldName
            . ', ' . $this->entityValueName
            . ', ' . $this->primaryEntityFieldName
            . ') VALUES '
        . implode(', ', $values) . ' RETURNING *';

        try {
            return $this->database->queryParams($query, $params);
        } catch (QueryException $e) {
            throw new CouldNotAddException($e->getMessage());
        }
    }

    /**
     * create entities by given array with entity id
     *
     * @param array<array<string,string>> $arr [array of raw entites got from entity primary table]
     * @return EntityModelInterface[] [array of entites]
     */
    protected function selectFields(array $arr): array
    {
        $entities = [];
        foreach ($arr as $entityRow) {
            $queryFields = 'SELECT ' . $this->fieldsTable . '.* FROM '
                . $this->fieldsTable . ' WHERE ' . $this->primaryEntityFieldName . ' = '
                . $entityRow[$this->primaryEntityFieldName];
            $fieldsRow   = $this->database->queryParams($queryFields, []);
            $entities[]  = $this->createFromRow($entityRow, $fieldsRow);
        }
        return $entities;
    }
}

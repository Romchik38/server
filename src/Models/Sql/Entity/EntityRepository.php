<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql\Entity;

use Romchik38\Server\Api\Models\Entity\EntityRepositoryInterface;
use Romchik38\Server\Api\Models\DatabaseInterface;
use Romchik38\Server\Api\Models\Entity\EntityFactoryInterface;
use Romchik38\Server\Api\Models\Entity\EntityModelInterface;
use Romchik38\Server\Models\Errors\{ 
    NoSuchEntityException, QueryExeption, CouldNotSaveException, 
    CouldNotDeleteException, CouldNotAddException
};

class EntityRepository implements EntityRepositoryInterface
{
    public function __construct(
        protected DatabaseInterface $database,
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
        $keys = [];
        $values = [];
        $params = [];
        $count = 0;

        foreach ($model->getAllEntityData() as $key => $value) {
            $count++;
            $params[] = '$' . $count;
            $keys[] = $key;
            $values[] = $value;
        }

        $query = 'INSERT INTO ' . $this->entityTable . ' (' . implode(', ', $keys) . ') VALUES ('
            . implode(', ', $params) . ') RETURNING *';
        try {
            $arr = $this->database->queryParams($query, $values);
            $entityRow = $arr[0];

            // 2 add fields data
            $entityId = (int)$entityRow[$this->primaryEntityFieldName];
            $fields = $model->getFieldsData();

            // @throws CouldNotAddException
            $fieldsRow = $this->insertFields($fields, $entityId);
            return $this->createFromRow($entityRow, $fieldsRow);
        } catch (QueryExeption $e) {
            throw new CouldNotAddException($e->getMessage());
        }
    }

    /**
     * Add fields to existing entity
     * 
     * @param array $fields [field_name => field_value, ...]
     * @param int $entityId 
     * @throws CouldNotAddException
     * @return EntityModelInterface [a fresh copy of given entity with new fields]
     */
    public function addFields(array $fields, EntityModelInterface $entity): EntityModelInterface {
        $entityRow = $entity->getAllEntityData();
        $entityId = $entityRow[$this->primaryEntityFieldName];
        // @throws CouldNotAddException
        $fieldsRow = $this->insertFields($fields, $entityId);
        return $this->createFromRow($entityRow, $fieldsRow);   
    }

    /**
     * create an empty entity
     *
     * @return EntityModelInterface
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
     * @return void
     */
    public function deleteById(int $id): void
    {
        $query = 'DELETE FROM ' . $this->entityTable . ' WHERE '
        . $this->primaryEntityFieldName . ' = $1';
        try {
            $this->database->queryParams($query, [$id]);
        } catch (QueryExeption $e) {
            throw new CouldNotDeleteException($e->getMessage());
        }
    }

    /**
     * Delete entity fields. 
     * Fields are values of $this->entityFieldName table
     * 
     * @param string[] $fields
     * @param EntityModelInterface $entity
     * @throws CouldNotDeleteException [when some errors occures]
     * @throws NoSuchEntityException [if given entity doesn't present]
     * @return EntityModelInterface [a fresh copy of the entity already without given fields]
     */
    public function deleteFields(array $fields, EntityModelInterface $entity): EntityModelInterface
    {
        $count = 0;
        $values = [];
        $params = [];
        foreach($fields as $field) {
            ++$count;
            $values[] = $this->entityFieldName . ' = ' . '$' . $count;
            $params[] = $field;
        }
        
        $entityId = $entity->getEntityData($this->primaryEntityFieldName);
        $params[] = $entityId;

        $query = 'DELETE FROM ' . $this->fieldsTable . ' WHERE (' . implode(" OR ", $values) 
        . ') AND ' . $this->primaryEntityFieldName . ' = $' . ++$count;

        try {
            $this->database->queryParams($query, [$params]);
            return $this->getById($entityId);
        } catch (QueryExeption $e) {
            throw new CouldNotDeleteException($e->getMessage());
        }
    }

    /**
     * find an entity by provided id
     *
     * @param integer $id
     * @throws NoSuchEntityException
     * @return EntityModelInterface
     */
    public function getById(int $id): EntityModelInterface
    {
        // 1. find an entity
        $query = 'SELECT * FROM '
            . $this->entityTable
            . ' WHERE ' . $this->primaryEntityFieldName . ' = $1';
        $params = [$id];
        $arr = $this->database->queryParams($query, $params);
        if (count($arr) === 0) {
            throw new NoSuchEntityException('row with id ' . $id
                . ' do not present in the ' . $this->primaryEntityFieldName . ' table');
        }
        $entityRow = $arr[0];
        // 2. select all fields
        $queryFields = 'SELECT * FROM '
        . $this->fieldsTable
        . ' WHERE ' . $this->primaryEntityFieldName . ' = $1';
        $paramsFields = [$id];
        $fieldsRow = $this->database->queryParams($queryFields, $paramsFields);

    return $this->createFromRow($entityRow, $fieldsRow);

    }

    /**
     * create a list of intities by provided expression 
     *
     * @param string $expression [use entity id or name]
     * @param array $params
     * @return EntityModelInterface[]
     */
    public function listByEntities(string $expression, array $params): array
    {
        // 1 select entities
        $query = 'SELECT ' . $this->entityTable . '.* FROM ' . $this->entityTable . ' ' . $expression;
        $arr = $this->database->queryParams($query, $params);

        // 2. select fields
        $entities = $this->selectFields($arr);
        return $entities;
    }

    public function  listByFields(string $expression, array $params): array {
        // select distinct entity_id from entity_field where field_name like '%defa%';

        // 1. select entities
        $query = 'SELECT ' . $this->entityTable . '.* FROM ' . $this->entityTable 
            . ' WHERE ' . $this->primaryEntityFieldName 
            . ' IN (SELECT DISTINCT ' . $this->fieldsTable . '.' . $this->primaryEntityFieldName
            . ' FROM ' . $this->fieldsTable . ' ' . $expression . ')';
        $arr = $this->database->queryParams($query, $params);
        
        // 2. select fields
        $entities = $this->selectFields($arr);
        return $entities;
    }

    /**
     * Save existing entity. Use add method if you want to save a new one
     * 
     * @param EntityModelInterface $model
     * @return EntityModelInterface [a fresh copy of given entity]
     */
    public function save(EntityModelInterface $model): EntityModelInterface
    {
        // 1 save an entity
        $params = [];
        $fields = [];
        $count = 0;
        foreach ($model->getAllEntityData() as $key => $value) {
            if ($key === $this->primaryEntityFieldName) {
                continue;
            }
            $count++;
            $param = '$' . $count;
            $params[] = $value;
            $fields[] = $key . ' = ' . $param;
        }

        $query = 'UPDATE ' . $this->entityTable . ' SET ' . implode(', ', $fields) 
            . ' WHERE ' . $this->entityTable . '.' . $this->primaryEntityFieldName 
            . ' = $' . ++$count . ' RETURNING *';
        
        $params[] = $this->primaryEntityFieldName;

        try {
            if (count($fields) > 0) {
                $arr = $this->database->queryParams($query, $params);
                $entityRow = $arr[0];
            } else {
                // only entity_id was provided with entity data, so no query needed
                $entityRow = $model->getAllEntityData();
            }
            // 2 save entity fields
            $params2 = [];
            $fields2 = [];
            $count2 = 0;
            foreach ($model->getFieldsData() as $key2 => $value2) {
                $count2++;
                $param2 = '$' . $count2;
                $params2[] = $value2;
                $fields2[] = $key2 . ' = ' . $param2;
            }

            $query2 = 'UPDATE ' . $this->fieldsTable . ' SET ' . implode(', ', $fields2) 
            . ' WHERE ' . $this->fieldsTable . '.' . $this->primaryEntityFieldName 
            . ' = $' . ++$count2 . ' RETURNING *';

            $params2[] = $this->primaryEntityFieldName;

            try {
                $fieldsRow = $this->database->queryParams($query2, $params2);
            } catch (QueryExeption $e) {
                throw new CouldNotSaveException($e->getMessage());
            }  
            // 3 return saved entity
            return $this->createFromRow($entityRow, $fieldsRow);
        } catch (QueryExeption $e) {
            throw new CouldNotSaveException($e->getMessage());
        }
    }

    /**
     * create an entity from row
     *
     * @param array $entityRow
     * @param array $fieldsRow
     * @return EntityModelInterface
     */
    protected function createFromRow(array $entityRow, array $fieldsRow): EntityModelInterface
    {
        $entity = $this->entityFactory->create();
        foreach ($entityRow as $key => $value) {
            $entity->setEntityData($key, $value);
        }

        foreach ($fieldsRow as $field) {
            $key = $field[$this->entityFieldName];
            $value = $field[$this->entityValueName];
            $entity->$key = $value;
        }

        return $entity;
    }

    /**
     * insert rows into fields table by provided id
     * 
     * @param array $fields [field_name => field_value, ...]
     * @param int $entityId 
     * @throws CouldNotAddException
     */
    protected function insertFields(array $fields, int $entityId): array {
        $values = [];
        $params = [];
        $count = 0;                    
                                    
        foreach ($fields as $key => $value) {
            $fullValue = '(';
            // field
            $count++;
            $params[] = $key;
            $fullValue = $fullValue . '$' . $count;
            // value
            $count++;
            $params[] = $value;
            $fullValue = $fullValue . ', $' . $count;
            // entity id
            $count++;
            $params[] = $entityId;
            $fullValue = $fullValue . ', $' . $count . ')';
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
            $fieldsRow = $this->database->queryParams($query, $params);
            return $fieldsRow;
        } catch (QueryExeption $e) {
            throw new CouldNotAddException($e->getMessage());
        }
    }

    /**
     * create entities by given array with entity id
     * 
     * @param array $arr [array of raw entites got from entity primary table]
     * @return EntityModelInterface[] [array of entites]
     */
    protected function selectFields(array $arr): array {
        $entities = [];
        foreach ($arr as $entityRow) {
            $queryFields = 'SELECT ' . $this->fieldsTable . '.* FROM ' 
                . $this->fieldsTable . ' WHERE ' . $this->primaryEntityFieldName . ' = ' 
                . $entityRow[$this->primaryEntityFieldName];
            $fieldsRow = $this->database->queryParams($queryFields, []);
            $entities[] = $this->createFromRow($entityRow, $fieldsRow);
        }
        return $entities;
    }
}

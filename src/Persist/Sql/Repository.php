<?php

declare(strict_types=1);

namespace Romchik38\Server\Persist\Sql;

use Romchik38\Server\Models\Errors\CouldNotAddException;
use Romchik38\Server\Models\Errors\CouldNotDeleteException;
use Romchik38\Server\Models\Errors\CouldNotSaveException;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Models\ModelFactoryInterface;
use Romchik38\Server\Models\ModelInterface;
use Romchik38\Server\Models\RepositoryInterface;

use function count;
use function implode;

class Repository implements RepositoryInterface
{
    public function __construct(
        protected DatabaseSqlInterface $database,
        protected ModelFactoryInterface $modelFactory,
        protected string $table,
        protected string $primaryFieldName
    ) {
    }

    public function add(ModelInterface $model): ModelInterface
    {
        $keys   = [];
        $values = [];
        $params = [];
        $count  = 0;
        foreach ($model->getAllData() as $key => $value) {
            $count++;
            $params[] = '$' . $count;
            $keys[]   = $key;
            $values[] = $value;
        }

        $query = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $keys) . ') VALUES ('
        . implode(', ', $params) . ') RETURNING *';
        try {
            $arr = $this->database->queryParams($query, $values);
            $row = $arr[0];
            return $this->createFromRow($row);
        } catch (QueryException $e) {
            throw new CouldNotAddException($e->getMessage());
        }
    }

    public function create(): ModelInterface
    {
        return $this->modelFactory->create();
    }

    public function deleteById(int|string $id): void
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE '
        . $this->primaryFieldName . ' = $1';
        try {
            $this->database->queryParams($query, [$id]);
        } catch (QueryException $e) {
            throw new CouldNotDeleteException($e->getMessage());
        }
    }

    public function getById(int|string $id): ModelInterface
    {
        $query  = 'SELECT ' . $this->table . '.* FROM ' . $this->table
        . ' WHERE ' . $this->primaryFieldName . ' = $1';
        $params = [$id];
        $arr    = $this->database->queryParams($query, $params);
        if (count($arr) === 0) {
            throw new NoSuchEntityException('row with id ' . $id
            . ' do not present in the ' . $this->table . ' table');
        }
        $row = $arr[0];

        return $this->createFromRow($row);
    }

    public function list(string $expression = '', array $params = []): array
    {
        $entities = [];

        $query = 'SELECT ' . $this->table . '.* FROM ' . $this->table . ' ' . $expression;
        $arr   = $this->database->queryParams($query, $params);
        foreach ($arr as $row) {
            $entities[] = $this->createFromRow($row);
        }

        return $entities;
    }

    public function save(ModelInterface $model): ModelInterface
    {
        $fields  = [];
        $params  = [];
        $counter = 0;
        foreach ($model->getAllData() as $key => $value) {
            $counter++;
            $fields[] = $key . ' = $' . $counter;
            $params[] = $value;
        }
        $query    = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $fields)
        . ' WHERE ' . $this->primaryFieldName . ' = $' . ++$counter . ' RETURNING *';
        $params[] = $model->getData($this->primaryFieldName);
        try {
            $arr = $this->database->queryParams($query, $params);
            $row = $arr[0];
            return $this->createFromRow($row);
        } catch (QueryException $e) {
            throw new CouldNotSaveException($e->getMessage());
        }
    }

    /**
     *  Create an entity from provided row
     *
     * @param array<string,string|null> $row
     */
    protected function createFromRow(array $row): ModelInterface
    {
        $entity = $this->modelFactory->create();
        foreach ($row as $key => $value) {
            $entity->setData($key, $value);
        }

        return $entity;
    }
}

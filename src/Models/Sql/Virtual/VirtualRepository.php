<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Sql\Virtual;

use Romchik38\Server\Api\Models\DatabaseInterface;
use Romchik38\Server\Api\Models\ModelFactoryInterface;
use Romchik38\Server\Api\Models\ModelInterface;
use Romchik38\Server\Api\Models\Virtual\VirtualRepositoryInterface;

class VirtualRepository implements VirtualRepositoryInterface
{

    public function __construct(
        protected DatabaseInterface $database,
        protected ModelFactoryInterface $modelFactory,
        protected array $selectFields,
        protected array $tables
    ) {
    }

    public function create(): ModelInterface
    {
        return $this->modelFactory->create();
    }

    public function list(string $expression, array $params): array
    {
        $entities = [];

        $query = 'SELECT ' . implode(', ', $this->selectFields) 
            . ' FROM ' . implode(', ', $this->tables) . ' ' . $expression;

        $arr = $this->database->queryParams($query, $params);
        foreach ($arr as $row) {
            $entities[] = $this->createFromRow($row);
        }

        return $entities;
    }

    /**
     * Create an entity from provided row
     * 
     * @param array $row ['field' => 'value', ...]
     * @return ModelInterface
     */
    protected function createFromRow(array $row): ModelInterface
    {
        $entity = $this->create();

        foreach ($row as $key => $value) {
            $entity->setData($key, $value);
        }

        return $entity;
    }

}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models;

use Romchik38\Server\Api\Models\ModelInterface;
use Romchik38\Server\Models\Errors\CouldNotDeleteException;
use Romchik38\Server\Models\Errors\CouldNotSaveException;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

interface RepositoryInterface
{
    /**
     * Creates a new empty entity
     */
    public function create(): ModelInterface;

    /**
     * Find an entity by provided id
     *
     * @throws NoSuchEntityException
     */
    public function getById(int|string $id): ModelInterface;

    /**
     * Returns a list of the Models
     *
     * @param string $expression - like "WHERE first_name = 'bob'"
     * @param array<int,string> $params
     * @return ModelInterface[]
     */
    public function list(string $expression, array $params): array;

    /**
     * inserts a row to the database
     */
    public function add(ModelInterface $model): ModelInterface;

    /**
     * Delete a row from the table
     *
     * @throws CouldNotDeleteException
     */
    public function deleteById(int|string $id): void;

    /**
     * Update an existing model
     *
     * @throws CouldNotSaveException
     */
    public function save(ModelInterface $model): ModelInterface;
}

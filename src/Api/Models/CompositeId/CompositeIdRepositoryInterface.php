<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\CompositeId;

use Romchik38\Server\Models\Errors\CouldNotSaveException;
use Romchik38\Server\Models\Errors\CouldNotDeleteException;
use Romchik38\Server\Models\Errors\CouldNotAddException;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

interface CompositeIdRepositoryInterface
{
    /**
     * Create a new empty entity
     *   
     * @return CompositeIdModelInterface
     */
    public function create(): CompositeIdModelInterface;

    /**
     * Search in the repository by provided id
     * 
     * @param CompositeIdDTOInterface $id
     * @throws NoSuchEntityException
     * @return CompositeIdModelInterface
     */
    public function getById(CompositeIdDTOInterface $id): CompositeIdModelInterface;

    /**
     * Search in the repository by provided condition
     * 
     * @param string $expression [condition]
     * @param array $params
     * @return CompositeIdModelInterface[]
     */
    public function list(string $expression, array $params): array;

    /**
     * Add new entity
     * 
     * @param CompositeIdModelInterface $model [a new entity to save]
     * @throws CouldNotAddException
     * @return CompositeIdModelInterface
     */
    public function add(CompositeIdModelInterface $model): CompositeIdModelInterface;

    /**
     * Delete existing entity by provided id
     * 
     * @param CompositeIdDTOInterface $id
     * @throws CouldNotDeleteException  
     * @return void
     */
    public function deleteById(CompositeIdDTOInterface $id): void;

    /**
     * Save existing model
     *
     * @param CompositeIdModelInterface $model
     * @throws CouldNotSaveException
     * @return CompositeIdModelInterface
     */
    public function save(CompositeIdModelInterface $model): CompositeIdModelInterface;
}

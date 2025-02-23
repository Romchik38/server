<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Entity;

use Romchik38\Server\Api\Models\Entity\EntityModelInterface;
use Romchik38\Server\Models\Errors\CouldNotAddException;
use Romchik38\Server\Models\Errors\CouldNotDeleteException;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

interface EntityRepositoryInterface
{
    public function add(EntityModelInterface $model): EntityModelInterface;

     /**
      * Add fields to existing entity
      *
      * @param array<string,string> $fields [field_name => field_value, ...]
      * @throws CouldNotAddException
      * @return EntityModelInterface [a fresh copy of given entity with new fields]
      */
    public function addFields(array $fields, EntityModelInterface $entity): EntityModelInterface;

    public function create(): EntityModelInterface;

    public function deleteById(int $id): void;

     /**
      * Delete entity fields.
      * Fields are values of $this->entityFieldName table
      *
      * @param string[] $fields
      * @throws CouldNotDeleteException [when some errors occures]
      * @throws NoSuchEntityException [if given entity doesn't present]
      * @return EntityModelInterface [a fresh copy of the entity already without given fields]
      */
    public function deleteFields(
        array $fields,
        EntityModelInterface $entity
    ): EntityModelInterface;

    /**
     * Find an entity by provided id
     *
     * @param int $id [entity id]
     * @throws NoSuchEntityException
     */
    public function getById(int $id): EntityModelInterface;

     /**
      * create a list of intities by provided expression
      *
      * @param string $expression [use entity id or name]
      * @param array<int,string> $params
      * @return EntityModelInterface[]
      */
    public function listByEntities(string $expression, array $params): array;

    /**
     * @param array<int,string> $params
     * @return EntityModelInterface[]
     */
    public function listByFields(string $expression, array $params): array;

    public function save(EntityModelInterface $model): EntityModelInterface;
}

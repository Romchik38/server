<?php 

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Entity;

use Romchik38\Server\Api\Models\Entity\EntityModelInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

interface EntityRepositoryInterface {
    public function add(EntityModelInterface $model): EntityModelInterface;
    public function addFields(array $fields, EntityModelInterface $entity): EntityModelInterface;
    public function create(): EntityModelInterface;
    public function deleteById(int $id): void;
    public function deleteFields(array $fields, EntityModelInterface $entity): EntityModelInterface;

    /**
     * Find an entity by provided id
     * @param int $id [entity id]
     * @throws NoSuchEntityException
     * @return EntityModelInterface
     */
    public function getById(int $id): EntityModelInterface;
    public function listByEntities(string $expression, array $params): array;
    public function listByFields(string $expression, array $params): array;
    public function save(EntityModelInterface $model): EntityModelInterface;
}
<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

use Romchik38\Server\Api\Models\Entity\EntityModelInterface;

class EntityModel implements EntityModelInterface {

    /**
     * stores entity primary fields
     * @var array<string,mixed> $entityData
     */
    private array $entityData = [];

    /**
     * Stores field/values of the entity
     * @var array<string,mixed> $fieldsData
     */
    private array $fieldsData = [];

    public function getFieldsData(): array {
        return $this->fieldsData;
    }

    /**
     * Get a value by provided field name
     * @return mixed - value or null on failure
     */
    public function __get(string $field): mixed {
        return $this->fieldsData[$field] ?? null;
    }

    /**
     * set field/value
     */
    public function __set(string $field, mixed $val): void {
        $this->fieldsData[$field] = $val;
    }

    public function getAllEntityData(): array {
        return $this->entityData;
    }

    public function getEntityData(string $key): mixed {
        return $this->entityData[$key];
    }

    public function setEntityData(string $key, mixed $value): EntityModelInterface {
        $this->entityData[$key] = $value;
        return $this;
    }

}
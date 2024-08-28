<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\Entity;

interface EntityModelInterface {

    /** FIELDS */
    public function getFieldsData(): array;

    /** ENTITY */
    // public function getEntityId(): int;                  << MOVE THIS TO SPECIFIC ENTITY
    // public function getName(): string;

    public function getEntityData(string $key);
    public function setEntityData(string $key, $value): EntityModelInterface;
    public function getAllEntityData(): array;
}
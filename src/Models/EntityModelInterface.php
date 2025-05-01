<?php

declare(strict_types=1);

namespace Romchik38\Server\Models;

interface EntityModelInterface
{
    /**
     * FIELDS
     *
     * @return array<string,mixed>
     */
    public function getFieldsData(): array;

    /** ENTITY */
    public function getEntityData(string $key): mixed;

    public function setEntityData(string $key, mixed $value): EntityModelInterface;

    /**
     * @return array<string,mixed>
     */
    public function getAllEntityData(): array;
}

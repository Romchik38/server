<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\CompositeId;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

/** Used in repository getById/deleteById methods */
interface CompositeIdDTOInterface extends DTOInterface
{
    /** 
     * Return an array with keys used in Id
     * 
     * @return array ['key', ...]
     */
    public function getIdKeys(): array;
}

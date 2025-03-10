<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Http\LinkTree;

use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOInterface;

/**
 * Represents a controller's tree
 * LinkTreeDTOFactoryInterface is responsable to create this entity
 */
interface LinkTreeDTOInterface extends LinkDTOInterface
{
    public const CHILDREN_FIELD = 'children';

    /**
     * @return LinkTreeDTOInterface[]
     */
    public function getChildren(): array;
}

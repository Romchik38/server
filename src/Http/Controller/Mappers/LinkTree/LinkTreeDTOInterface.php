<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\LinkTree;

use Romchik38\Server\Http\Controller\Mappers\Link\LinkDTOInterface;

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

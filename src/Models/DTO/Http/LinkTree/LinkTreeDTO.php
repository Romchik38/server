<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Http\LinkTree;

use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOInterface;
use Romchik38\Server\Models\DTO\Http\Link\LinkDTO;

/**
 * LinkTreeDTO entity. Represents a http link to visit. Has children.
 * Use LinkTreeDTOFactory to create it.
 * @api
 */
class LinkTreeDTO extends LinkDTO implements LinkTreeDTOInterface
{
    /**
     * @param string $name link name like 'Catalog'
     * @param string $description link description lide 'Product catalog 2024'
     * @param string $url link url like /products
     * @param LinkTreeDTOInterface[] $children Sublinks
     */
    public function __construct(
        string $name,
        string $description,
        string $url,
        array $children
    ) {
        parent::__construct($name, $description, $url);
        $this->data[LinkTreeDTOInterface::CHILDREN_FIELD] = $children;
    }

    public function getChildren(): array
    {
        return $this->data[LinkTreeDTOInterface::CHILDREN_FIELD];
    }
}

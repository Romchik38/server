<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Http\LinkTree;

use Romchik38\Server\Models\Errors\InvalidArgumentException;

/**
 * Creates LinktreeDTO entity
 * @api
 */
interface LinkTreeDTOFactoryInterface
{
    /**
     * @param string $name link name like 'Catalog'
     * @param string $description link description lide 'Product catalog 2024'
     * @param string $url link url like /products
     * @param LinkTreeDTOInterface[] $children Sublinks
     * @throws InvalidArgumentException name, description and url length must be greater than 0
     * @return LinkTreeDTOInterface Created entity
     */
    public function create(
        string $name,
        string $description,
        string $url,
        array $children
    ): LinkTreeDTOInterface;
}

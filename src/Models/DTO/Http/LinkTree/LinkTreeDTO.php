<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Http\LinkTree;

use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOInterface;
use Romchik38\Server\Models\DTO\Http\Link\LinkDTO;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

/**
 * LinkTreeDTO entity. Represents a http link to visit. Has children.
 * @api
 */
class LinkTreeDTO extends LinkDTO implements LinkTreeDTOInterface
{
    /**
     * @param string $name link name like 'Catalog'
     * @param string $description link description lide 'Product catalog 2024'
     * @param string $url link url like /products
     * @param LinkTreeDTOInterface[] $children Sublinks
     * @throws InvalidArgumentException name, description and url length must be greater than 0
     */
    public function __construct(
        string $name,
        string $description,
        string $url,
        array $children
    ) {
        if (
            strlen($name) === 0 ||
            strlen($description) === 0 ||
            strlen($url) === 0
        ) {
            throw new InvalidArgumentException('Arguments name, description, url must not be blank');
        }

        parent::__construct($name, $description, $url);
        $this->data[LinkTreeDTOInterface::CHILDREN_FIELD] = $children;
    }

    public function getChildren(): array
    {
        return $this->data[LinkTreeDTOInterface::CHILDREN_FIELD];
    }
}

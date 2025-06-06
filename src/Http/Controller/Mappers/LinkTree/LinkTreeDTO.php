<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\LinkTree;

use InvalidArgumentException;
use Romchik38\Server\Http\Controller\Mappers\Link\LinkDTO;
use Romchik38\Server\Http\Controller\Mappers\LinkTree\LinkTreeDTOInterface;

use function strlen;

/**
 * LinkTreeDTO entity. Represents a http link to visit. Has children.
 */
class LinkTreeDTO extends LinkDTO implements LinkTreeDTOInterface
{
    /**
     * @param string $name link name like 'Catalog'
     * @param string $description link description lide 'Product catalog 2024'
     * @param string $url link url like /products
     * @param LinkTreeDTOInterface[] $children Sublinks
     * @throws InvalidArgumentException - Name, description and url length must be greater than 0.
     */
    public function __construct(
        string $name,
        string $description,
        string $url,
        protected readonly array $children
    ) {
        if (
            strlen($name) === 0 ||
            strlen($description) === 0 ||
            strlen($url) === 0
        ) {
            throw new InvalidArgumentException('Arguments name, description, url must not be blank');
        }

        parent::__construct($name, $description, $url);
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}

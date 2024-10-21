<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Http\LinkTree;

use InvalidArgumentException;
use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOInterface;

/**
 * Use to create an LinkTreeDTO entity
 * @api
 */
class LinkTreeDTOFactory implements LinkTreeDTOFactoryInterface
{
    public function create(
        string $name,
        string $description,
        string $url,
        array $children
    ): LinkTreeDTOInterface {
        /** 1. Length check */
        if (
            strlen($name) === 0 ||
            strlen($description) === 0 ||
            strlen($url) === 0
        ) {
            throw new InvalidArgumentException('Arguments name, description, url must not be blank');
        }
        /** 2. Extends this class to create more checks*/

        return new LinkTreeDTO($name, $description, $url, $children);
    }
}

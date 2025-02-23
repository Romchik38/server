<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Http\Link;

use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOInterface;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

use function strlen;

class LinkDTOFactory implements LinkDTOFactoryInterface
{
    public function create(string $name, string $description, string $url): LinkDTOInterface
    {
        /** 1. Length check */
        if (
            strlen($name) === 0 ||
            strlen($description) === 0 ||
            strlen($url) === 0
        ) {
            throw new InvalidArgumentException('Arguments name, description, url must not be blank');
        }
        /** 2. Extends this class to create more checks*/

        return new LinkDTO($name, $description, $url);
    }
}

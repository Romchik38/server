<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\Html\Link;

use Romchik38\Server\Api\Models\DTO\Html\Link\LinkDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Html\Link\LinkDTOInterface;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

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

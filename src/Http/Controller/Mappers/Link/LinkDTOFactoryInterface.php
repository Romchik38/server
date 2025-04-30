<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Http\Link;

use InvalidArgumentException;

interface LinkDTOFactoryInterface
{
    /**
     * @throws InvalidArgumentException - Name, description and url length must be greater than 0.
     */
    public function create(
        string $name,
        string $description,
        string $url,
    ): LinkDTOInterface;
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Html\Link;
use Romchik38\Server\Models\Errors\InvalidArgumentException;

interface LinkDTOFactoryInterface
{
    /**
     * @throws InvalidArgumentException name, description and url length must be greater than 0
     */
    public function create(
        string $name,
        string $description,
        string $url,
    ): LinkDTOInterface;
}

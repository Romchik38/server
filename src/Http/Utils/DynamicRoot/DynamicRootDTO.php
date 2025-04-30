<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\DynamicRoot;

use Romchik38\Server\Models\DTO;

class DynamicRootDTO extends DTO implements DynamicRootDTOInterface
{
    public function __construct(
        protected string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}

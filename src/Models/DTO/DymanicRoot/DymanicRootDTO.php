<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\DTO\DymanicRoot;

use Romchik38\Server\Api\Models\DTO\DymanicRoot\DymanicRootDTOInterface;
use Romchik38\Server\Models\DTO;

class DymanicRootDTO extends DTO implements DymanicRootDTOInterface
{
    public function __construct(
        protected string $name
    ) {}
    public function getName(): string
    {
        return $this->name;
    }
}

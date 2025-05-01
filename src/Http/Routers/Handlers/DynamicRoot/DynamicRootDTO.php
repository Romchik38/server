<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\DynamicRoot;

class DynamicRootDTO implements DynamicRootDTOInterface
{
    public function __construct(
        protected readonly string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}

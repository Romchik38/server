<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\DynamicRoot;

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

<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\DynamicRoot;

/** dynamic root entity */
interface DynamicRootDTOInterface
{
    public function getName(): string;
}

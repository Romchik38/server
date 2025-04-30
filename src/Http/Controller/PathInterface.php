<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller;

interface PathInterface
{
    /** @return array<int,string> */
    public function __invoke(): array;
}

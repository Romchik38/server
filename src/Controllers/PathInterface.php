<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers;

interface PathInterface
{
    /** @return array<int,string> - Non empty array of non empty strings */
    public function __invoke(): array;
}

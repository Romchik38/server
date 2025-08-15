<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views;

use Romchik38\Server\Http\Views\Errors\ViewBuildException;

interface ViewInterface
{
    /** @throws ViewBuildException */
    public function toString(): string;
}

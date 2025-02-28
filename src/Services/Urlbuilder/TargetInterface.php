<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Urlbuilder;

use InvalidArgumentException;
use Romchik38\Server\Controllers\PathInterface;

interface TargetInterface
{
    /**
     * @throws InvalidArgumentException - On empty array.
     * */
    public function fromPath(PathInterface $path): string;
}

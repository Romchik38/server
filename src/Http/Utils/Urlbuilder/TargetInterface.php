<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use InvalidArgumentException;
use Romchik38\Server\Http\Controller\PathInterface;

interface TargetInterface
{
    /**
     * @throws InvalidArgumentException - On empty array.
     * */
    public function fromPath(PathInterface $path): string;
}

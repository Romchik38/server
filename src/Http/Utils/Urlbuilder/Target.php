<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Controller\PathInterface;

use function array_slice;

class Target extends RootlessTarget
{
    public function fromPath(PathInterface $path): string
    {
        $parts     = $path();
        $firstPath = $parts[0];
        if ($firstPath === ControllerInterface::ROOT_NAME) {
            $parts = array_slice($parts, 1);
        }
        return parent::fromPath(new Path($parts));
    }
}

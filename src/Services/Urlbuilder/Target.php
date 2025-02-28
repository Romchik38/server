<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Urlbuilder;

use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\PathInterface;

use function array_slice;
use function implode;

class Target implements TargetInterface
{
    public function fromPath(PathInterface $path): string
    {
        $parts     = $path();
        $firstPath = $parts[0];
        if ($firstPath === ControllerTreeInterface::ROOT_NAME) {
            $parts = array_slice($parts, 1);
        }
        return '/' . implode('/', $parts);
    }
}

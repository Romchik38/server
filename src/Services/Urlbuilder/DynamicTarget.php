<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Urlbuilder;

use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\PathInterface;
use Romchik38\Server\Services\DynamicRoot\DynamicRootInterface;

use function implode;

class DynamicTarget implements TargetInterface
{
    public function __construct(
        protected readonly DynamicRootInterface $dynamicRoot
    ) {
    }

    public function fromPath(PathInterface $path): string
    {
        $parts     = $path();
        $firstPath = $parts[0];
        if ($firstPath === ControllerTreeInterface::ROOT_NAME) {
            $parts[0] = $this->dynamicRoot->getCurrentRoot()->getName();
        }
        return '/' . implode('/', $parts);
    }
}

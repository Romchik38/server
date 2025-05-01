<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\PathInterface;
use Romchik38\Server\Http\Utils\DynamicRoot\DynamicRootInterface;

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
        if ($firstPath === ControllerInterface::ROOT_NAME) {
            $parts[0] = $this->dynamicRoot->getCurrentRoot()->getName();
        }
        return '/' . implode('/', $parts);
    }
}

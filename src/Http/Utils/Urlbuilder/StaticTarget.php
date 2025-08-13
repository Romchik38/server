<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Name;
use Romchik38\Server\Http\Controller\PathInterface;

use function implode;

class StaticTarget implements TargetInterface
{
    public function __construct(
        public readonly Name $staticRoot
    ) {
    }

    public function fromPath(PathInterface $path): string
    {
        $parts     = $path();
        $firstPath = $parts[0];
        if ($firstPath === ControllerInterface::ROOT_NAME) {
            $parts[0] = ($this->staticRoot)();
        }
        return '/' . implode('/', $parts);
    }
}

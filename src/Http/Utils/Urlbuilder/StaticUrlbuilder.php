<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Name;
use Romchik38\Server\Http\Controller\Path;

use function array_unshift;

class StaticUrlbuilder extends AbstractUrlbuilder implements StaticUrlbuilderInterface
{
    public readonly bool $hasRootName;

    public function __construct(
        public readonly Path $staticPath,
        string $scheme = '',
        string $authority = ''
    ) {
        $firstPart = ($this->staticPath)()[0];
        if ($firstPart === ControllerInterface::ROOT_NAME) {
            $this->hasRootName = true;
        } else {
            $this->hasRootName = false;
        }

        parent::__construct(new RootlessTarget(), $scheme, $authority);
    }

    public function withRoot(
        string $rootName,
        array $params = [],
        string $fragment = ''
    ): string {
        $newRoot = new Name($rootName);
        $parts   = ($this->staticPath)();
        if ($this->hasRootName) {
            // root replace with new root
            $parts[0] = $newRoot();
        } else {
            // add new root
            array_unshift($parts, $newRoot());
        }
        return $this->fromPath(new Path($parts), $params, $fragment);
    }
}

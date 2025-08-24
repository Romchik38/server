<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares\Result;

use Romchik38\Server\Http\Controller\Path;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootInterface;

final class DynamicPathMiddlewareResult extends DefaultPathMiddlewareResult
{
    public function __construct(
        Path $path,
        public readonly DynamicRootInterface $dynamicRoot
    ) {
        parent::__construct($path);
    }

    public function getDynamicRoot(): DynamicRootInterface
    {
        return $this->dynamicRoot;
    }
}

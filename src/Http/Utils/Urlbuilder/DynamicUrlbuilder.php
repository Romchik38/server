<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Utils\Urlbuilder;

use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootInterface;

class DynamicUrlbuilder extends AbstractUrlbuilder
{
    public function __construct(
        DynamicRootInterface $dynamicRoot,
        string $scheme = '',
        string $authority = ''
    ) {
        parent::__construct(new DynamicTarget($dynamicRoot), $scheme, $authority);
    }
}

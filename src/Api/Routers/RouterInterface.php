<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Routers;

use Romchik38\Server\Api\Results\RouterResultInterface;

interface RouterInterface
{
    public function execute(): RouterResultInterface;
}

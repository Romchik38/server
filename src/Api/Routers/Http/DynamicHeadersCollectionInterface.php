<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Routers\Http;

use Romchik38\Server\Api\Routers\Http\RouterHeadersInterface;

interface DynamicHeadersCollectionInterface
{
    /**
     * @return RouterHeadersInterface|null 
     */
    public function getHeader(string $method, string $path, string $actionType): RouterHeadersInterface|null;
}

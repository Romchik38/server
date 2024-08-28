<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers\Actions;

use Romchik38\Server\Controllers\Errors\DynamicActionNotFoundException;

interface DynamicActionInterface extends ActionInterface
{

    /** 
     * The last part of the chain.
     * Returns the result to client
     * 
     * @throws DynamicActionNotFoundException [if the route is unknown]
     * @return string [result]
     */
    public function execute(string $dynamicRoute): string;

    /**
     * returns an array of routes names
     * 
     * @return string[]
     */
    public function getRoutes(): array;
}

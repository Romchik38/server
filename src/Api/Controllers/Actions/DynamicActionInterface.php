<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers\Actions;

use Romchik38\Server\Api\Models\DTO\DynamicRoute\DynamicRouteDTOInterface;
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
     * Returns an array of DynamicRouteDTOs.
     * Used in the mapper services to represents the Action
     * 
     * @return array<int,DynamicRouteDTOInterface>
     */
    public function getDynamicRoutes(): array;

    /** Description of concrete dynamic route */
    public function getDescription(string $dynamicRoute): string;
}

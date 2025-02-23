<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers\Actions;

use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Models\DTO\DynamicRoute\DynamicRouteDTOInterface;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;

interface DynamicActionInterface extends ActionInterface
{
    /**
     * The last part of the chain.
     * Returns the result to client
     *
     * @throws ActionNotFoundException [if the route is unknown]
     * @return ResponseInterface Action Response
     */
    public function execute(string $dynamicRoute): ResponseInterface;

    /**
     * Returns an array of DynamicRouteDTOs.
     * Used in the mapper services to represents the Action
     *
     * @return array<int,DynamicRouteDTOInterface>
     */
    public function getDynamicRoutes(): array;

    /** Description of concrete dynamic route
     *
     * @throws DynamicActionLogicException When description was not found
     */
    public function getDescription(string $dynamicRoute): string;
}

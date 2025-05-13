<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Actions;

use Romchik38\Server\Http\Controller\Actions\ActionInterface;
use Romchik38\Server\Http\Controller\Dto\DynamicRouteDTOInterface;
use Romchik38\Server\Http\Controller\Errors\DynamicActionLogicException;

interface DynamicActionInterface extends ActionInterface
{
    /**
     * Returns an array of DynamicRouteDTOs.
     * Used in the mapper services to represents the Action
     *
     * @return array<int,DynamicRouteDTOInterface>
     */
    public function getDynamicRoutes(): array;

    /** Description of concrete dynamic route
     *
     * @throws DynamicActionLogicException - When description was not found.
     */
    public function getDescription(string $dynamicRoute): string;
}

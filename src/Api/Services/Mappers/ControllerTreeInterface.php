<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Mappers;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;

interface ControllerTreeInterface
{
    public const ROOT_NAME = 'root';
    /**
     * Map controller tree to controller dto tree. Used for breadcrumbs.
     *
     * @param ControllerInterface $controller Current controller. Only to transfer process control
     * @return ControllerDTOInterface [root controller dto]
     */
    public function getRootControllerDTO(ControllerInterface $controller): ControllerDTOInterface;

    /**
     * map controller tree to one line dto tree
     * every member can have only one parent and one child
     *
     * @param ControllerInterface $controller Current controller. Only to transfer process control
     * @return ControllerDTOInterface root controller dto
     */
    public function getOnlyLineRootControllerDTO(
        ControllerInterface $controller,
        string $action
    ): ControllerDTOInterface;
}

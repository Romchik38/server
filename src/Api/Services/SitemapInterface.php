<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;

interface SitemapInterface
{
    /** 
     * map controller tree to controller dto tree 
     * 
     * @param ControllerInterface $controller [current controller]
     * @return ControllerDTOInterface [root controller dto]
     */
    public function getRootControllerDTO(ControllerInterface $controller): ControllerDTOInterface;

    /** 
     * map controller tree to one line dto tree 
     * every member can have only one parent and one child
     *  
     * @param ControllerInterface $controller current controller
     * @return ControllerDTOInterface root controller dto
     */
    public function getOnlyLineRootControllerDTO(ControllerInterface $controller, string $action): ControllerDTOInterface;
}

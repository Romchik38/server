<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Mappers\LinkTree\Http;

use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOInterface;

/**
 * Creates LinkTreeDTO
 * @api
 */
interface LinkTreeInterface
{
    /** convert controllerDTO to linkTreeDTO */
    public function getLinkTreeDTO(ControllerDTOInterface $rootControllerDTO): LinkTreeDTOInterface;
}

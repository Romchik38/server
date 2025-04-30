<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Mappers\LinkTree\Http;

use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOInterface;
use Romchik38\Server\Http\Controller\Dto\ControllerDTOInterface;

/**
 * Creates LinkTreeDTO
 */
interface LinkTreeInterface
{
    /** convert controllerDTO to linkTreeDTO */
    public function getLinkTreeDTO(
        ControllerDTOInterface $rootControllerDto
    ): LinkTreeDTOInterface;
}

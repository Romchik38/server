<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Models\DTO\Html\Breadcrumb;

use Romchik38\Server\Api\Models\DTO\DTOInterface;

interface BreadcrumbDTOInterface extends DTOInterface
{
    const PREV_FIELD = 'prev';

    public function getPrev(): BreadcrumbDTOInterface|null;

}

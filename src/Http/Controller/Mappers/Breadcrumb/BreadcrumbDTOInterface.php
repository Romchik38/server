<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\Breadcrumb;

use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOInterface;

interface BreadcrumbDTOInterface extends LinkDTOInterface
{
    public const PREV_FIELD = 'prev';

    public function getPrev(): BreadcrumbDTOInterface|null;
}

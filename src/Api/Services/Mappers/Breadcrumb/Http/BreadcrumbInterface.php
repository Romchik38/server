<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Http\Breadcrumb\BreadcrumbDTOInterface;

interface BreadcrumbInterface
{
    public const HOME_PLACEHOLDER = 'home';
    /**
     * Return a chain of breadcrumbs
     */
    public function getBreadcrumbDTO(ControllerInterface $controller, string $action): BreadcrumbDTOInterface;
}

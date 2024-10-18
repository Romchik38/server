<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\Breadcrumb\Http;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Html\Breadcrumb\BreadcrumbDTOInterface;

interface BreadcrumbInterface
{
    const HOME_PLACEHOLDER = 'home';
    /**
     * Return a chain of breadcrumbs
     */
    public function getBreadcrumbDTO(ControllerInterface $controller, string $action): BreadcrumbDTOInterface;
}

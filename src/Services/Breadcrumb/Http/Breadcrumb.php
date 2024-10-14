<?php

declare(strict_types=1);

namespace Romchik38\Server\Models\Mappers;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Models\DTO\Html\Breadcrumb\BreadcrumbDTOFactoryInterface;
use Romchik38\Server\Api\Services\SitemapInterface;

/** @todo create an Interface */
class Breadcrumb
{
    public function __construct(
        protected SitemapInterface $sitemapService,
        protected BreadcrumbDTOFactoryInterface $breadcrumbDTOFactory,
    ) {}

    public function getBreadcrumbDTO(ControllerInterface $controller, string $action) {
        $controllerDTO = $this->sitemapService->getOnlyLineRootControllerDTO($controller, $action);
        
    }
}

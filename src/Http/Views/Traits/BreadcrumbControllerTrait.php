<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Views\Traits;

use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbDTOInterface;
use Romchik38\Server\Http\Views\Errors\ViewBuildException;

use function array_unshift;

/**
 * $breadcrumbService is an instance of BreadcrumbInterface
 */
trait BreadcrumbControllerTrait
{
    /**
     * @throws ViewBuildException - If controller was not set.
     * @return array<int,BreadcrumbDTOInterface>
     */
    protected function prepareBreadcrumbs(): array
    {
        if ($this->controller === null) {
            throw new ViewBuildException('Can\'t prepare breadcrums: controller was not set');
        }

        $breadcrumbDto = $this->breadcrumbService->getBreadcrumbDTO(
            $this->controller,
            $this->action
        );
        $items         = [];
        $stop          = false;
        $current       = $breadcrumbDto;
        while ($stop === false) {
            $stop = true;
            array_unshift($items, $current);
            $next = $current->getPrev();
            if ($next !== null) {
                $stop    = false;
                $current = $next;
            }
        }
        return $items;
    }
}

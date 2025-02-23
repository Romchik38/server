<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Mappers\Breadcrumb\Http;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Models\DTO\Http\Breadcrumb\BreadcrumbDTOInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Models\DTO\Http\Breadcrumb\BreadcrumbDTO;

use function array_merge;
use function array_push;
use function array_slice;
use function count;
use function implode;

class Breadcrumb implements BreadcrumbInterface
{
    protected string $currentRoot = ControllerTreeInterface::ROOT_NAME;

    public function __construct(
        protected ControllerTreeInterface $controllerTreeService,
        protected DynamicRootInterface|null $dynamicRoot = null
    ) {
    }

    public function getBreadcrumbDTO(ControllerInterface $controller, string $action): BreadcrumbDTOInterface
    {
        /**
         * 1 Set Dynamic root if exist
         */
        if ($this->dynamicRoot !== null) {
            $this->currentRoot = $this->dynamicRoot->getCurrentRoot()->getName();
        }

        /** 2. Get ControllerDTOInterface */
        $controllerDTO = $this->controllerTreeService->getOnlyLineRootControllerDTO($controller, $action);

        /** 4. get breadcrumbDTO */
        return $this->mapControllerDTOtoBreadcrumbDTO($controllerDTO, null);
    }

    /** @return array<int,string[]>*/
    protected function getPathsFromControllerDTO(ControllerDTOInterface $dto): array
    {
        $stop    = false;
        $paths   = [];
        $current = $dto;
        while ($stop === false) {
            $stop     = true;
            $paths[]  = array_merge($current->getPath(), [$current->getName()]);
            $children = $current->getChildren();
            if (count($children) > 0) {
                $stop    = false;
                $current = $children[0];
            }
        }
        return $paths;
    }

    protected function mapControllerDTOtoBreadcrumbDTO(
        ControllerDTOInterface $controllerDTO,
        BreadcrumbDTOInterface|null $prev,
    ): BreadcrumbDTOInterface {
        $name = $controllerDTO->getName();
        $path = $controllerDTO->getPath();

        array_push($path, $name);

        $path[0] = $this->currentRoot;

        $firstPath = $path[0];
        if ($firstPath === ControllerTreeInterface::ROOT_NAME) {
            $path = array_slice($path, 1);
        }

        if ($name === ControllerTreeInterface::ROOT_NAME) {
            $name = BreadcrumbInterface::HOME_PLACEHOLDER;
        }

        $url = '/' . implode('/', $path);

        $element = new BreadcrumbDTO(
            $name,
            $controllerDTO->getDescription(),
            $url,
            $prev
        );

        $children = $controllerDTO->getChildren();
        if (count($children) > 0) {
            $child = $children[0];
            return $this->mapControllerDTOtoBreadcrumbDTO($child, $element);
        } else {
            return $element;
        }
    }
}

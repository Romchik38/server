<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\Breadcrumb;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Dto\ControllerDTOInterface;
use Romchik38\Server\Http\Controller\Mappers\ControllerTree\ControllerTreeInterface;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootInterface;

use function array_merge;
use function array_push;
use function array_slice;
use function count;
use function implode;

class Breadcrumb implements BreadcrumbInterface
{
    protected string $currentRoot = ControllerInterface::ROOT_NAME;

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
        $controllerDto = $this->controllerTreeService->getOnlyLineRootControllerDTO($controller, $action);

        /** 4. get breadcrumbDTO */
        return $this->mapControllerDTOtoBreadcrumbDTO($controllerDto, null);
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
        ControllerDTOInterface $controllerDto,
        BreadcrumbDTOInterface|null $prev,
    ): BreadcrumbDTOInterface {
        $name = $controllerDto->getName();
        $path = $controllerDto->getPath();

        array_push($path, $name);

        $path[0] = $this->currentRoot;

        $firstPath = $path[0];
        if ($firstPath === ControllerInterface::ROOT_NAME) {
            $path = array_slice($path, 1);
        }

        if ($name === ControllerInterface::ROOT_NAME) {
            $name = BreadcrumbInterface::HOME_PLACEHOLDER;
        }

        $url = '/' . implode('/', $path);

        $element = new BreadcrumbDTO(
            $name,
            $controllerDto->getDescription(),
            $url,
            $prev
        );

        $children = $controllerDto->getChildren();
        if (count($children) > 0) {
            $child = $children[0];
            return $this->mapControllerDTOtoBreadcrumbDTO($child, $element);
        } else {
            return $element;
        }
    }
}

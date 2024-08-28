<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Sitemap;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Services\SitemapInterface;
use Romchik38\Server\Services\Errors\CantCreateSitemapElement;

class Sitemap implements SitemapInterface
{

    public function __construct(
        protected readonly ControllerDTOFactoryInterface $controllerDTOFactory
    ) {}

    /** 
     * for breadcrumbs
     */
    public function getOnlyLineRootControllerDTO(ControllerInterface $controller, string $action): ControllerDTOInterface
    {
        $rootDTO = $this->createItem(null, $controller, $action);
        return $rootDTO;
    }

    /** maps whole controller tree to controller model tree with root element in a head*/
    public function getRootControllerDTO(ControllerInterface $controller): ControllerDTOInterface
    {
        $first = $this->getFirst($controller);
        $ControllerDTO = $this->createElement($first);
        return $ControllerDTO;
    }

    /** 
     * used in getOnlyLineRootControllerDTO
     */
    protected function createItem(ControllerDTOInterface|null $child, ControllerInterface $controller, string $action = ''): ControllerDTOInterface
    {

        $current = $controller;
        $path = [];
        while ($current->getCurrentParent() !== null) {
            $parent = $current->getCurrentParent();
            array_unshift($path, $parent->getName());
            $current = $parent;
        }

        if ($action !== '') {
            $name = $action;
            $path[] = $controller->getName();
            $element = $this->controllerDTOFactory->create(
                $name,
                $path,
                []
            );

            return $this->createItem($element, $controller);
        }

        $name = $controller->getName();

        if ($child !== null) {
            $element =  $this->controllerDTOFactory->create(
                $name,
                $path,
                [$child]
            );
        } else {
            $element =  $this->controllerDTOFactory->create(
                $name,
                $path,
                []
            );
        }

        if ($controller->getCurrentParent() !== null) {
            return $this->createItem($element, $controller->getCurrentParent());
        } else {
            return $element;
        }
    }

    /** 
     * used in getRootControllerDTO
     */
    protected function createElement(ControllerInterface $element, $parentName = '', $parrentPath = [])
    {
        if ($element->isPublic() === false) {
            throw new CantCreateSitemapElement('Element ' . $element->getName() . ' is not public');
        }

        $rowPath = $parrentPath;
        $children = $element->getChildren();

        if (count($children) === 0) {
            $lastPath = $rowPath;
            if ($parentName !== '') {
                $lastPath[] = $parentName;
            }

            $allChi = $this->addDynamicChildren($element, [], [], $lastPath);

            $lastElement = $this->controllerDTOFactory->create(
                $element->getName(),
                $lastPath,
                $allChi
            );
            return $lastElement;
        }

        /** @var Controller $child */
        $elementName = $element->getName();
        $rowChi = [];
        if ($parentName !== '') {
            array_unshift($rowPath, $parentName);
        }
        $childrenNames = [];
        foreach ($children as $child) {
            $childrenNames[] = $child->getName();
            try {
                $rowElem = $this->createElement($child, $elementName, $rowPath);
                $rowChi[] = $rowElem;
            } catch (CantCreateSitemapElement $e) {
                continue;
            }
        }

        $allChi = $this->addDynamicChildren($element, $childrenNames, $rowChi, $rowPath);

        $row = $this->controllerDTOFactory->create($elementName, $rowPath, $allChi); // 2
        return $row;
    }

    /** 
     * used in getRootControllerDTO
     */
    protected function addDynamicChildren(
        ControllerInterface $element,
        array $childrenNames,
        array $rowChi,
        array $rowPath
    ): array {
        $allChi = $rowChi;
        $dynamicRoutes = $element->getDynamicRoutes();
        foreach ($dynamicRoutes as $dynamicRoute) {
            // skip dynamic routes which names equal to children names
            if (array_search($dynamicRoute, $childrenNames) !== false) {
                continue;
            }
            $dynElemPath = $rowPath;
            $dynElemPath[] = $element->getName();
            $rowDynamicElem = $this->controllerDTOFactory->create(
                $dynamicRoute,
                $dynElemPath,
                []
            );
            $allChi[] = $rowDynamicElem;
        }
        return $allChi;
    }

    /** 
     * returns the first root element with all tree
     */
    protected function getFirst(ControllerInterface $controller): ControllerInterface
    {
        $stop = false;
        $current = $controller;
        while ($stop === false) {
            $stop = true;
            $parent = $current->getCurrentParent();
            if ($parent !== null) {
                $stop = false;
                $current = $parent;
            }
        }
        return $current;
    }
}

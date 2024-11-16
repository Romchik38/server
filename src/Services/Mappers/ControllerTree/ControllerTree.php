<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Mappers\ControllerTree;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;
use Romchik38\Server\Services\Errors\CantCreateControllerTreeElement;

class ControllerTree implements ControllerTreeInterface
{
    /** 
     * Creates a line from current controller to root parent, without all children.
     */
    public function getOnlyLineRootControllerDTO(ControllerInterface $controller, string $action): ControllerDTOInterface
    {
        return $this->createItem(null, $controller, $action);
    }

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
            $element = new ControllerDTO(
                $name,
                $path,
                []
            );

            return $this->createItem($element, $controller);
        }

        $name = $controller->getName();

        if ($child !== null) {
            $element =  new ControllerDTO(
                $name,
                $path,
                [$child]
            );
        } else {
            $element =  new ControllerDTO(
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
    protected function createElement(ControllerInterface $element, $parentName = '', $parrentPath = []): ControllerDTOInterface
    {
        if ($element->isPublic() === false) {
            throw new CantCreateControllerTreeElement('Element ' . $element->getName() . ' is not public');
        }

        $rowPath = $parrentPath;
        $children = $element->getChildren();

        $description = $element->getDescription() ?? $element->getName();

        /** Case 1 - has no children */
        if (count($children) === 0) {
            $lastPath = $rowPath;
            if ($parentName !== '') {
                $lastPath[] = $parentName;
            }

            /** add dynamic children */
            $allChi = $this->addDynamicChildren($element, [], [], $lastPath);

            /** create DTO */
            $lastElement = new ControllerDTO(
                $element->getName(),
                $lastPath,
                $allChi,
                $description
            );
            return $lastElement;
        }

        /** Case 2 - has children */
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
            } catch (CantCreateControllerTreeElement $e) {
                continue;
            }
        }

        $allChi = $this->addDynamicChildren($element, $childrenNames, $rowChi, $rowPath);

        $row = new ControllerDTO($elementName, $rowPath, $allChi, $description);
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
        $dynamicRouteDTOs = $element->getDynamicRoutes();
        foreach ($dynamicRouteDTOs as $dto) {
            $dynamicRoute = $dto->name();
            // skip dynamic routes which names equal to children names
            if (array_search($dynamicRoute, $childrenNames) !== false) {
                continue;
            }
            $dynElemPath = $rowPath;
            $dynElemPath[] = $element->getName();
            $rowDynamicElem = new ControllerDTO(
                $dynamicRoute,
                $dynElemPath,
                [],
                $dto->description()
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

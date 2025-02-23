<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Mappers\ControllerTree;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Models\DTO\Controller\ControllerDTO;
use Romchik38\Server\Services\Errors\CantCreateControllerTreeElement;

use function array_search;
use function array_unshift;
use function count;
use function sprintf;

class ControllerTree implements ControllerTreeInterface
{
    /**
     * Creates a line from current controller to root parent, without all children.
     */
    public function getOnlyLineRootControllerDTO(
        ControllerInterface $controller,
        string $action
    ): ControllerDTOInterface {
        return $this->createItem(null, $controller, $action);
    }

    public function getRootControllerDTO(ControllerInterface $controller): ControllerDTOInterface
    {
        $first = $this->getFirst($controller);
        return $this->createElement($first);
    }

    /**
     * used in getOnlyLineRootControllerDTO
     */
    protected function createItem(
        ControllerDTOInterface|null $child,
        ControllerInterface $controller,
        string $action = ''
    ): ControllerDTOInterface {
        /** Case 1 - default */
        /** Case 2 - dynamic */
        $current = $controller;
        $path    = [];
        while ($current->getCurrentParent() !== null) {
            $parent = $current->getCurrentParent();
            array_unshift($path, $parent->getName());
            $current = $parent;
        }

        if ($action !== '') {
            $name        = $action;
            $description = $controller->getDescription($action);
            if ($description === '') {
                $description = $name;
            }
            $path[]  = $controller->getName();
            $element = new ControllerDTO(
                $name,
                $path,
                [],
                $description
            );

            return $this->createItem($element, $controller);
        }

        $name        = $controller->getName();
        $description = $controller->getDescription();
        if ($description === '') {
            $description = $name;
        }
        if ($child !== null) {
            $element = new ControllerDTO(
                $name,
                $path,
                [$child],
                $description
            );
        } else {
            $element = new ControllerDTO(
                $name,
                $path,
                [],
                $description
            );
        }

        if ($controller->getCurrentParent() !== null) {
            return $this->createItem($element, $controller->getCurrentParent());
        } else {
            return $element;
        }
    }

    /**
     * Used in getRootControllerDTO
     *
     * @param array<int,string> $parrentPath
     */
    protected function createElement(
        ControllerInterface $element,
        string $parentName = '',
        array $parrentPath = []
    ): ControllerDTOInterface {
        if ($element->isPublic() === false) {
            throw new CantCreateControllerTreeElement(
                sprintf('Element %s is not public', $element->getName())
            );
        }

        $rowPath  = $parrentPath;
        $children = $element->getChildren();

        $description = $element->getDescription();
        if ($description === '') {
            $description = $element->getName();
        }

        /** Case 1 - has no children */
        if (count($children) === 0) {
            $lastPath = $rowPath;
            if ($parentName !== '') {
                $lastPath[] = $parentName;
            }

            /** add dynamic children */
            $allChi = $this->addDynamicChildren($element, [], [], $lastPath);

            /** create DTO */
            return new ControllerDTO(
                $element->getName(),
                $lastPath,
                $allChi,
                $description
            );
        }

        /** Case 2 - has children */
        $elementName = $element->getName();
        $rowChi      = [];
        if ($parentName !== '') {
            array_unshift($rowPath, $parentName);
        }
        $childrenNames = [];
        foreach ($children as $child) {
            $childrenNames[] = $child->getName();
            try {
                $rowElem  = $this->createElement($child, $elementName, $rowPath);
                $rowChi[] = $rowElem;
            } catch (CantCreateControllerTreeElement $e) {
                continue;
            }
        }

        $allChi = $this->addDynamicChildren($element, $childrenNames, $rowChi, $rowPath);

        return new ControllerDTO($elementName, $rowPath, $allChi, $description);
    }

    /**
     * Used in getRootControllerDTO
     *
     * @param array<int,string> $childrenNames
     * @param array<int,ControllerDTOInterface> $rowChi
     * @param array<int,string> $rowPath
     * @return array<int,ControllerDTOInterface>
     */
    protected function addDynamicChildren(
        ControllerInterface $element,
        array $childrenNames,
        array $rowChi,
        array $rowPath
    ): array {
        $allChi           = $rowChi;
        $dynamicRouteDtos = $element->getDynamicRoutes();
        foreach ($dynamicRouteDtos as $dto) {
            $dynamicRoute = $dto->name();
            // skip dynamic routes which names equal to children names
            if (array_search($dynamicRoute, $childrenNames) !== false) {
                continue;
            }
            $dynElemPath    = $rowPath;
            $dynElemPath[]  = $element->getName();
            $rowDynamicElem = new ControllerDTO(
                $dynamicRoute,
                $dynElemPath,
                [],
                $dto->description()
            );
            $allChi[]       = $rowDynamicElem;
        }
        return $allChi;
    }

    /**
     * returns the first root element with all tree
     */
    protected function getFirst(ControllerInterface $controller): ControllerInterface
    {
        $stop    = false;
        $current = $controller;
        while ($stop === false) {
            $stop   = true;
            $parent = $current->getCurrentParent();
            if ($parent !== null) {
                $stop    = false;
                $current = $parent;
            }
        }
        return $current;
    }
}

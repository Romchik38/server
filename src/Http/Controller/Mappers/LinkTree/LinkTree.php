<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Controller\Mappers\LinkTree;

use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\Dto\ControllerDTOInterface;
use Romchik38\Server\Http\Controller\Mappers\Breadcrumb\BreadcrumbInterface;
use Romchik38\Server\Http\Routers\Handlers\DynamicRoot\DynamicRootInterface;

use function array_map;
use function array_push;
use function array_slice;
use function implode;
use function urlencode;

/**
 * Maps ControllerDTO to LinkTreeDTO
 */
class LinkTree implements LinkTreeInterface
{
    protected string $currentRoot = ControllerInterface::ROOT_NAME;

    public function __construct(
        protected DynamicRootInterface|null $dynamicRoot = null
    ) {
    }

    /**
     * @return LinkTreeDTOInterface Root link with all children tree
     */
    public function getLinkTreeDTO(ControllerDTOInterface $rootControllerDto): LinkTreeDTOInterface
    {
        /** 1 Set Dynamic root if exist */
        if ($this->dynamicRoot !== null) {
            $this->currentRoot = $this->dynamicRoot->getCurrentRoot()->getName();
        }

        return $this->buildLinkTreeDTOHash($rootControllerDto);
    }

    protected function buildLinkTreeDTOHash(ControllerDTOInterface $element): LinkTreeDTOInterface
    {
        $name        = $element->getName();
        $description = $element->getDescription();
        $path        = $element->getPath();

        array_push($path, $name);

        $path[0] = $this->currentRoot;

        $firstPath = $path[0];
        if ($firstPath === ControllerInterface::ROOT_NAME) {
            $path = array_slice($path, 1);
        }

        if ($name === ControllerInterface::ROOT_NAME) {
            $name = BreadcrumbInterface::HOME_PLACEHOLDER;
        }

        if ($description === ControllerInterface::ROOT_NAME) {
            $description = BreadcrumbInterface::HOME_PLACEHOLDER;
        }

        $url = '/' . implode('/', array_map(fn($part)=> urlencode($part), $path));

        $children    = $element->getChildren();
        $dtoChildren = [];
        foreach ($children as $child) {
            // do something with children
            $dtoChild      = $this->buildLinkTreeDTOHash($child);
            $dtoChildren[] = $dtoChild;
        }

        return new LinkTreeDTO(
            $name,
            $description,
            $url,
            $dtoChildren
        );
    }
}

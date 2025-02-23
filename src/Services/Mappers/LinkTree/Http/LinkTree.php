<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Mappers\LinkTree\Http;

use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Api\Services\Mappers\LinkTree\Http\LinkTreeInterface;
use Romchik38\Server\Models\DTO\Http\LinkTree\LinkTreeDTO;

use function array_push;
use function array_slice;
use function implode;

/**
 * Maps ControllerDTO to LinkTreeDTO
 */
class LinkTree implements LinkTreeInterface
{
    protected string $currentRoot = ControllerTreeInterface::ROOT_NAME;

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
        if ($firstPath === ControllerTreeInterface::ROOT_NAME) {
            $path = array_slice($path, 1);
        }

        if ($name === ControllerTreeInterface::ROOT_NAME) {
            $name = BreadcrumbInterface::HOME_PLACEHOLDER;
        }

        if ($description === ControllerTreeInterface::ROOT_NAME) {
            $description = BreadcrumbInterface::HOME_PLACEHOLDER;
        }

        $url = '/' . implode('/', $path);

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

<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Mappers\LinkTree\Http;

use Romchik38\Server\Api\Models\DTO\Controller\ControllerDTOInterface;
use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOCollectionInterface;
use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\Http\LinkTree\LinkTreeDTOInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Mappers\Breadcrumb\Http\BreadcrumbInterface;
use Romchik38\Server\Api\Services\Mappers\SitemapInterface;
use Romchik38\Server\Api\Models\DTO\Http\Link\LinkDTOInterface;
use Romchik38\Server\Api\Services\Mappers\LinkTree\Http\LinkTreeInterface;

/** 
 * Maps ControllerDTO to LinkTreeDTO
 * 
 * @api
 */
class LinkTree implements LinkTreeInterface
{
    protected string $currentRoot = SitemapInterface::ROOT_NAME;

    public function __construct(
        protected LinkTreeDTOFactoryInterface $linkTreeDTOFactory,
        protected LinkDTOCollectionInterface|null $linkDTOCollection = null,
        protected DynamicRootInterface|null $dynamicRoot = null
    ) {}

    /** 
     * @return LinkTreeDTOInterface Root link with all children tree
     */
    public function getLinkTreeDTO(ControllerDTOInterface $rootControllerDTO): LinkTreeDTOInterface
    {
        /** 1 Set Dynamic root if exist */
        if ($this->dynamicRoot !== null) {
            $this->currentRoot = $this->dynamicRoot->getCurrentRoot()->getName();
        }

        $linkHash = [];
        /** 2. Get all available LinkDTOs if linkDTOCollection was provided */
        if ($this->linkDTOCollection !== null) {
            $linkDTOs = $this->linkDTOCollection->getLinksByPaths();
            foreach ($linkDTOs as $linkDTO) {
                $linkHash[$linkDTO->getUrl()] = $linkDTO;
            }
        }

        /** 3. Build controllerDTO hash */
        $rootLinkTreeDTO = $this->buildLinkTreeDTOHash($rootControllerDTO, $linkHash);

        return $rootLinkTreeDTO;
    }

    /**
     * @param LinkDTOInterface[] $hash
     */
    protected function buildLinkTreeDTOHash(ControllerDTOInterface $element, $hash = []): LinkTreeDTOInterface
    {
        $name = $element->getName();
        $path = $element->getPath();

        array_push($path, $name);

        $path[0] = $this->currentRoot;

        $firstPath = $path[0];
        if ($firstPath === SitemapInterface::ROOT_NAME) {
            $path = array_slice($path, 1);
        }

        if ($name === SitemapInterface::ROOT_NAME) {
            $name = BreadcrumbInterface::HOME_PLACEHOLDER;
        }

        $url = '/' . implode('/', $path);

        $children = $element->getChildren();
        $dtoChildren = [];
        foreach ($children as $child) {
            // do something with children
            $dtoChild = $this->buildLinkTreeDTOHash($child, $hash);
            $dtoChildren[] = $dtoChild;
        }
        $description = $name;
        /** @var LinkDTOInterface $linkDTO */
        $linkDTO = $hash[$url] ?? null;
        if ($linkDTO !== null) {
            $name = $linkDTO->getName();
            $description = $linkDTO->getDescription();
        }
        $dto = $this->linkTreeDTOFactory->create(
            $name,
            $description,
            $url,
            $dtoChildren
        );

        return $dto;
    }
}

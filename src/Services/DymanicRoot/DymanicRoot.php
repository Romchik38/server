<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\DymanicRoot;

use Romchik38\Server\Api\Models\DTO\DymanicRoot\DymanicRootDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\DymanicRoot\DymanicRootDTOInterface;
use Romchik38\Server\Api\Services\DymanicRoot\DymanicRootInterface;
use Romchik38\Server\Services\Errors\EarlyAccessToCurrentRootError;

class DymanicRoot implements DymanicRootInterface
{
    protected readonly DymanicRootDTOInterface $defaultRoot;
    protected DymanicRootDTOInterface|null $currentRoot = null;

    /**
     * @var DymanicRootDTOInterface[] $rootList
     */
    protected readonly array $rootList;

    public function __construct(
        string $defaultRootName,
        array $rootNamesList,
        DymanicRootDTOFactoryInterface $dymanicRootDTOFactory
    ) {
        $this->defaultRoot = $dymanicRootDTOFactory->create($defaultRootName);
        $list = [];
        foreach ($rootNamesList as $rootName) {
            $rootDTO = $dymanicRootDTOFactory->create($rootName);
            $list[] = $rootDTO;
        }
        $this->rootList = $list;
    }

    public function getDefaultRoot(): DymanicRootDTOInterface
    {
        return $this->defaultRoot;
    }

    public function getRootList(): array
    {
        return $this->rootList;
    }

    public function getRootNames(): array
    {
        $names = [];
        foreach ($this->rootList as $root) {
            $names[] = $root->getName();
        }
        return $names;
    }

    public function getCurrentRoot(): DymanicRootDTOInterface {
        if ($this->currentRoot === null) {
            throw new EarlyAccessToCurrentRootError('Current dynamic root does\'t setted up');
        }
        return $this->currentRoot;
    }

    public function setCurrentRoot(string $rootName): bool
    {
        foreach ($this->rootList as $root) {
            if ($root->getName() === $rootName) {
                $this->currentRoot = $root;
                return true;
            }
        }
        return false;
    }
}

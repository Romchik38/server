<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\DynamicRoot;

class DynamicRoot implements DynamicRootInterface
{
    protected readonly DynamicRootDTOInterface $defaultRoot;
    protected DynamicRootDTOInterface|null $currentRoot = null;

    /** @var DynamicRootDTOInterface[] $rootList */
    protected readonly array $rootList;

    /** @param array<int,string> $rootNamesList */
    public function __construct(
        string $defaultRootName,
        array $rootNamesList
    ) {
        $this->defaultRoot = new DynamicRootDTO($defaultRootName);
        $list              = [];
        foreach ($rootNamesList as $rootName) {
            $rootDto = new DynamicRootDTO($rootName);
            $list[]  = $rootDto;
        }
        $this->rootList = $list;
    }

    public function getDefaultRoot(): DynamicRootDTOInterface
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

    public function getCurrentRoot(): DynamicRootDTOInterface
    {
        if ($this->currentRoot === null) {
            throw new EarlyAccessToCurrentRootErrorException('Current dynamic root does\'t setted up');
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

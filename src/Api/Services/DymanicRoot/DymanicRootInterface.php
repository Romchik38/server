<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Services\DymanicRoot;

use Romchik38\Server\Api\Models\DTO\DymanicRoot\DymanicRootDTOInterface;

interface DymanicRootInterface
{
    const DEFAULT_ROOT_FIELD = 'default_root';
    const ROOT_LIST_FIELD = 'root_list';

    /** return default root */
    public function getDefaultRoot(): DymanicRootDTOInterface;

    /** 
     * return a list of root entities
     * the list defined in the config file
     * 
     * @return DymanicRootDTOInterface[]
     */
    public function getRootList(): array;

    /** 
     * @return string[] all root names from the list
     */
    public function getRootNames(): array;

    /**
     * Give an access to current root entity
     * 
     * About error: in reality it can't be because of:
     *    1 step - the current root is setted up in the Router
     *    2 step - then the root can be accessed in the services, controllers etc
     * 
     * @throws EarlyAccessToCurrentRootError When current root wasn't set
     * @return DymanicRootDTOInterface The root entity from the current request, setted by Router
     */
    public function getCurrentRoot(): DymanicRootDTOInterface;

    /**
     * @return bool true on success / false on fail
     */
    public function setCurrentRoot(string $rootName): bool;
}

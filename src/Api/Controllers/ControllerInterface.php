<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers;

use Romchik38\Server\Api\Models\DTO\DynamicRoute\DynamicRouteDTOInterface;
use Romchik38\Server\Api\Results\Controller\ControllerResultInterface;

interface ControllerInterface
{
    const NOT_FOUND_ERROR_MESSAGE = 'Requested url was not found on the server. Please check it and try again.';
    const PATH_SEPARATOR = '<>';
    const PATH_DYNAMIC_ALL = '*';
    /**
     * add parrent to this controller
     * 
     * @param ControllerInterface $parent
     * @return void
     */
    public function addParent(ControllerInterface $parent): void;

    /** transfers control to next controller */
    public function execute(array $elements): ControllerResultInterface;

    /** 
     * can controller be shown to user in the controllerTree
     */
    public function isPublic(): bool;

    /** @return string controller name */
    public function getName(): string;

    /**
     * Controller's description
     * 
     * @throws ControllerLogicException On non existing dynamic route
     * @return string 
     * */
    public function getDescription(string $dynamicRoute = ''): string;

    /** 
     * return a child by given controller name 
     * 
     * @param string $name [controller name]
     * @throws NoSuchControllerException
     * @return ControllerInterface
     */
    public function getChild(string $name): ControllerInterface;

    /** 
     * @return array<string,ControllerInterface> all children 
     * */
    public function getChildren(): array;

    /**
     * return the parent in this concrete flow 
     *   or null if it is root controller
     * 
     * @return ControllerInterface|null [parrent controller]
     */
    public function getCurrentParent(): ControllerInterface|null;
    
    /**
     * return an array of dynamic route DTOs or empty []
     * 
     * @return array<int,DynamicRouteDTOInterface>
     */
    public function getDynamicRoutes(): array;

    /**
     * return all parrent of the current controller
     * so we can trace all possible paths to this controller
     * 
     * @return ControllerInterface[] [parents]
     */
    public function getParents(): array;

    /** Returns full path to controller 
     * @param string $route Dynamic Action route
     */
    public function getFullPath(string $route = ''): array;

    /**
     * add child controller to the children list
     * 
     * @param ControllerInterface $child [a child to add]
     * @throws CantCreateControllerChain when try to add the root controller as a child
     * @return self [this controller]
     */
    public function setChild(ControllerInterface $child): self;

    /**
     * set the parent in this concrete flow
     * 
     * @param ControllerInterface $currentParent [parrent]
     * @return void
     */
    public function setCurrentParent(ControllerInterface $currentParent): void;
}

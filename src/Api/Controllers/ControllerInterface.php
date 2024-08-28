<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers;

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
     * can controller be shown to user in the sitemap
     */
    public function isPublic(): bool;

    /** return the controller name */
    public function getName(): string;

    /** 
     * return a child by given controller name 
     * 
     * @param string $name [controller name]
     * @throws NoSuchControllerException
     * @return ControllerInterface
     */
    public function getChild(string $name): ControllerInterface;

    /** return all children controllers */
    public function getChildren(): array;

    /**
     * return an array of dynamic route's names or empty []
     * 
     * @return string[]
     */
    public function getDynamicRoutes(): array;

    /**
     * return the parent in this concrete flow 
     *   or null if it is root controller
     * 
     * @return ControllerInterface|null [parrent controller]
     */
    public function getCurrentParent(): ControllerInterface|null;

    /**
     * return all parrent of the current controller
     * so we can trace all possible paths to this controller
     * 
     * @return ControllerInterface[] [parents]
     */
    public function getParents(): array;

    /**
     * add child controller to the children list
     * 
     * @param ControllerInterface $child [a child to add]
     * @return ControllerInterface [this controller]
     */
    public function setChild(ControllerInterface $child): ControllerInterface;

    /**
     * set the parent in this concrete flow
     * 
     * @param ControllerInterface $currentParent [parrent]
     * @return void
     */
    public function setCurrentParent(ControllerInterface $currentParent): void;
}

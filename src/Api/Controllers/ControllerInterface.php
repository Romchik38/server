<?php

declare(strict_types=1);

namespace Romchik38\Server\Api\Controllers;

use Romchik38\Server\Api\Models\DTO\DynamicRoute\DynamicRouteDTOInterface;
use Romchik38\Server\Controllers\Errors\CantCreateControllerChainException;
use Romchik38\Server\Controllers\Errors\ControllerLogicException;
use Romchik38\Server\Controllers\Errors\NoSuchControllerException;
use Romchik38\Server\Controllers\Errors\NotFoundException;

interface ControllerInterface
{
    public const NOT_FOUND_ERROR_MESSAGE = 'Requested url was not found on the server. Please check it and try again.';
    public const PATH_SEPARATOR          = '<>';
    public const PATH_DYNAMIC_ALL        = '*';
    /**
     * add parrent to this controller
     */
    public function addParent(ControllerInterface $parent): void;

    /**
     * transfers control to next controller
     *
     * @param array<int,string> $elements - Chain path ['controller_name', 'or_action_name']
     * @throws ControllerLogicException - On empty $elements.
     * @throws NotFoundException
     * */
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
     * @throws ControllerLogicException - On non existing dynamic route.
     * */
    public function getDescription(string $dynamicRoute = ''): string;

    /**
     * return a child by given controller name
     *
     * @param string $name [controller name]
     * @throws NoSuchControllerException
     */
    public function getChild(string $name): ControllerInterface;

    /**
     * @return array<string,ControllerInterface> - all children
     * */
    public function getChildren(): array;

    /**
     * return the parent in this concrete flow
     *   or null if it is root controller
     *
     * @return ControllerInterface|null - parrent controller
     */
    public function getCurrentParent(): ControllerInterface|null;

    /**
     * Return an array of dynamic route DTOs or empty []
     *
     * @return array<int,DynamicRouteDTOInterface>
     */
    public function getDynamicRoutes(): array;

    /**
     * Returns full path to controller
     *
     * @param string $route Dynamic Action route
     * @return array<int,string>
     */
    public function getFullPath(string $route = ''): array;

    /**
     * Returns all parrent of the current controller,
     * so we can trace all possible paths to this controller
     *
     * @return array<int,ControllerInterface> - [parents]
     */
    public function getParents(): array;

    /**
     * Add child controller to the children list
     *
     * @param ControllerInterface $child [a child to add]
     * @throws CantCreateControllerChainException - When try to add the root controller as a child.
     * @return self - this controller
     */
    public function setChild(ControllerInterface $child): self;

    /**
     * Sets the parent in a concrete flow
     *
     * @param ControllerInterface $currentParent [parrent]
     */
    public function setCurrentParent(ControllerInterface $currentParent): void;
}

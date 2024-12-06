<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Results\Controller\ControllerResultFactoryInterface;
use Romchik38\Server\Api\Results\Controller\ControllerResultInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\CantCreateControllerChain;
use Romchik38\Server\Controllers\Errors\ControllerLogicException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Controllers\Errors\NoSuchControllerException;
use Romchik38\Server\Controllers\Errors\NotFoundException;

/**
 * Controller tasks:
 * 
 *   0 - the path does not match with controller path
 *      0.1 - throw NotFoundException
 *   1  the path equal with controller path
 *      1.1 - there is a next controller
 *          - transfer control to next controller
 *      1.2 - if there is no next controller
 *          1.2.1 - execute action if dynamic not present
 *              1.2.1.1 - if action present 
 *                  1.2.1.1.1 - execute
 *                      1.2.1.1.1.1 - return result
 *                      1.2.1.1.1.2 - catch NotFound Action Error and throw own
 *              1.2.1.2 - if no action present
 *                  1.2.1.2.1 - throw NotFoundException
 *          1.2.2 - execute dynamic action if present
 *              1.2.1.1 - dynamic action present, route is known
 *                  1.2.1.1.1 - return the result
 *              1.2.1.2 - dynamic action present, but route is unknown
 *                  1.2.1.2.1 - throw NotFoundException
 *              1.2.1.3 - dynamic action present, but we have at least one more next control element in the path
 *                  1.2.1.3.1 - throw NotFoundException
 *          1.2.3 - if dynamic action not present 
 *              1.2.3.1 - throw NotFoundException
 */
class Controller implements ControllerInterface
{
    protected array $children = [];
    protected array $parents = [];
    protected Controller|null $currentParent = null;

    /**
     * must have Action
     * may have DynamicAction
     */
    public function __construct(
        protected readonly string $path,
        protected readonly bool $publicity = false,
        protected ControllerResultFactoryInterface|null $controllerResultFactory = null,
        protected readonly DefaultActionInterface|null $action = null,
        protected readonly DynamicActionInterface|null $dynamicAction = null
    ) {
        $hasAction = false;
        if ($this->action !== null) {
            $this->action->setController($this);
            $hasAction = true;
        }
        if ($this->dynamicAction !== null) {
            $this->dynamicAction->setController($this);
            $hasAction = true;
        }
        if ($hasAction === true && is_null($this->controllerResultFactory)) {
            throw new ControllerLogicException('Controller Result Factory needed to hold result from Action');
        }
    }

    /** @todo test */
    public function addParent(ControllerInterface $parent): void
    {
        $this->parents[] = $parent;
    }

    /** @todo test */
    public function execute(array $elements): ControllerResultInterface
    {
        if (count($elements) === 0) {
            throw new \RuntimeException('Controller error: path not found');
        }

        $route = array_shift($elements);
        if ($route === $this->path) {
            if (count($elements) === 0) {
                // execute this default action
                $fullPath = $this->getFullPath();
                if ($this->action !== null) {
                    try {
                        $response = $this->action->execute();
                        return $this->controllerResultFactory->create($response, $fullPath, ActionInterface::TYPE_DEFAULT_ACTION);
                    } catch (ActionNotFoundException) {
                        // 1.2.1.1.1.2 - catch NotFound Action Error and throw own
                        throw new NotFoundException(ControllerInterface::NOT_FOUND_ERROR_MESSAGE);
                    }
                } else {
                    // 1.2.1.2.1 - throw NotFoundException
                    throw new NotFoundException(ControllerInterface::NOT_FOUND_ERROR_MESSAGE);
                }
            } else {
                $nextRoute = $elements[0];
                // check for controller
                try {
                    $nextController = $this->getChild($nextRoute);
                    $nextController->setCurrentParent($this);
                    return $nextController->execute($elements);
                } catch (NoSuchControllerException $e) {
                    // we do not have next controller
                    // execute dinamic action
                    if (count($elements) === 1) {
                        if ($this->dynamicAction === null) {
                            //1.2.3.1 - throw NotFoundException
                            throw new NotFoundException(ControllerInterface::NOT_FOUND_ERROR_MESSAGE);
                        }
                        try {
                            $fullPath = $this->getFullPath($nextRoute);
                            $response = $this->dynamicAction->execute($nextRoute);
                            return $this->controllerResultFactory->create($response, $fullPath, ActionInterface::TYPE_DYNAMIC_ACTION);
                        } catch (ActionNotFoundException $e) {
                            //  1.2.1.2.1 - throw NotFoundException
                            throw new NotFoundException(ControllerInterface::NOT_FOUND_ERROR_MESSAGE);
                        }
                    }

                    // 1.2.1.3.1 - throw NotFoundException                
                    throw new NotFoundException(ControllerInterface::NOT_FOUND_ERROR_MESSAGE);
                }
            }
        } else {
            //0.1 - throw NotFoundException
            throw new NotFoundException(ControllerInterface::NOT_FOUND_ERROR_MESSAGE);
        }
    }

    public function isPublic(): bool
    {
        return $this->publicity;
    }

    public function getName(): string
    {
        return $this->path;
    }

    public function getDescription(string $dynamicRoute = ''): string
    {
        if (strlen($dynamicRoute) === 0) {
            if ($this->action === null) {
                return $this->path;
            } else {
                return $this->action->getDescription();
            }
        } else {
            if ($this->dynamicAction === null) {
                throw new ControllerLogicException(
                    sprintf(
                        'Description for dynamic route %s cannot be created because dynamic action not exist',
                        $dynamicRoute
                    )
                );
            }
            try {
                return $this->dynamicAction->getDescription($dynamicRoute);
            } catch (DynamicActionLogicException) {
                throw new ControllerLogicException(
                    sprintf('Description for dynamic route %s not exist', $dynamicRoute)
                );
            }
        }
    }

    public function getChild(string $name): Controller
    {
        return $this->children[$name] ??
            throw new NoSuchControllerException('children with name: ' . $name . ' does not exist');
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getCurrentParent(): Controller|null
    {
        return $this->currentParent;
    }

    /** @todo test */
    public function getDynamicRoutes(): array
    {
        if ($this->dynamicAction === null) {
            return [];
        }
        return $this->dynamicAction->getDynamicRoutes();
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function getFullPath(string $route = ''): array
    {
        $fullPath = [$this->path];
        if ($route !== '') {
            array_push($fullPath, $route);
        }
        $stop = false;
        $nextParrent = $this->currentParent;
        while ($stop === false) {
            $stop = true;
            if ($nextParrent === null) {
                return $fullPath;
            } else {
                array_unshift($fullPath, $nextParrent->getName());
                $nextParrent = $nextParrent->getCurrentParent();
                $stop = false;
            }
        }
    }

    public function setChild(ControllerInterface $child): Controller
    {
        $name = $child->getName();
        /** root controller must be one */
        if ($name === ControllerTreeInterface::ROOT_NAME) {
            throw new CantCreateControllerChain(
                'Controller with name ' . ControllerTreeInterface::ROOT_NAME . '  can\'t be a child'
            );
        }
        $this->children[$name] = $child;
        $child->addParent($this);
        return $this;
    }

    public function setCurrentParent(ControllerInterface $currentParent): void
    {
        $this->currentParent = $currentParent;
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Results\Controller\ControllerResultFactoryInterface;
use Romchik38\Server\Api\Results\Controller\ControllerResultInterface;
use Romchik38\Server\Controllers\Errors\DynamicActionNotFoundException;
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
        if ($this->action !== null) {
            $this->action->setController($this);
        }
        if ($this->dynamicAction !== null) {
            $this->dynamicAction->setController($this);
        }
    }
    
    public function isPublic(): bool {
        return $this->publicity;
    }

    public function getName(): string
    {
        return $this->path;
    }

    public function getChild(string $name): Controller
    {
        return $this->children[$name] ??
            throw new NoSuchControllerException('children with name: ' . $name . ' does not exist');
    }

    public function setChild(ControllerInterface $child): Controller
    {
        $name = $child->getName();
        $this->children[$name] = $child;
        $child->addParent($this);
        return $this;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getDynamicRoutes(): array
    {
        if ($this->dynamicAction === null) {
            return [];
        }
        return $this->dynamicAction->getRoutes();
    }

    public function getCurrentParent(): Controller|null
    {
        return $this->currentParent;
    }

    public function setCurrentParent(ControllerInterface $currentParent): void
    {
        $this->currentParent = $currentParent;
    }

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
                    $response = $this->action->execute();
                    return $this->controllerResultFactory->create($response, $fullPath, ActionInterface::TYPE_ACTION);
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
                        } catch (DynamicActionNotFoundException $e) {
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

    public function getParents(): array
    {
        return $this->parents;
    }

    public function addParent(ControllerInterface $parent): void
    {
        $this->parents[] = $parent;
    }

    protected function getFullPath(string $route = ''): array
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
}

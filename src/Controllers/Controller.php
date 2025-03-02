<?php

declare(strict_types=1);

namespace Romchik38\Server\Controllers;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Controllers\Actions\DefaultActionInterface;
use Romchik38\Server\Api\Controllers\Actions\DynamicActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Controllers\Middleware\RequestMiddlewareInterface;
use Romchik38\Server\Api\Controllers\Middleware\ResponseMiddlewareInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Controllers\Errors\ActionNotFoundException;
use Romchik38\Server\Controllers\Errors\CantCreateControllerChainException;
use Romchik38\Server\Controllers\Errors\ControllerLogicException;
use Romchik38\Server\Controllers\Errors\DynamicActionLogicException;
use Romchik38\Server\Controllers\Errors\NoSuchControllerException;
use Romchik38\Server\Controllers\Errors\NotFoundException;

use function array_push;
use function array_shift;
use function array_unshift;
use function count;
use function sprintf;
use function strlen;

/**
 * Controller tasks:
 *
 *   0 - the path does not match with controller path
 *      0.1 - throw NotFoundException
 *   1  the path equal with controller path
 *      1.1 - there is a next controller
 *          - transfer control to next controller
 *      1.2 - if there is no next controller
 *          ..... - excute request middlewares
 *                  - middleware return a response - stop execution and return this response.
 *                  - middleware return null - do nothing
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
    /** @var array<string,ControllerInterface> $children */
    protected array $children = [];

    /** @var array<int,RequestMiddlewareInterface> $requestMiddlewares */
    protected array $requestMiddlewares = [];

    /** @var array<int,ResponseMiddlewareInterface> $responseMiddlewares */
    protected array $responseMiddlewares = [];

    /** @var array<int,ControllerInterface> $parents */
    protected array $parents = [];

    protected ControllerInterface|null $currentParent = null;

    public function __construct(
        protected readonly string $name,
        protected readonly bool $publicity = false,
        protected readonly DefaultActionInterface|null $action = null,
        protected readonly DynamicActionInterface|null $dynamicAction = null
    ) {
        if (strlen($name) === 0) {
            throw new InvalidArgumentException('Controller name is empty');
        }
        if ($this->action !== null) {
            $this->action->setController($this);
        }
        if ($this->dynamicAction !== null) {
            $this->dynamicAction->setController($this);
        }
    }

    public function addRequestMiddleware(RequestMiddlewareInterface $middleware): self
    {
        $this->requestMiddlewares[] = $middleware;
        return $this;
    }

    public function addResponseMiddleware(ResponseMiddlewareInterface $middleware): self
    {
        $this->responseMiddlewares[] = $middleware;
        return $this;
    }

    public function addParent(ControllerInterface $parent): void
    {
        $this->parents[] = $parent;
    }

    /**
     * @todo test
     * */
    public function execute(array $elements): ResponseInterface
    {
        if (count($elements) === 0) {
            throw new ControllerLogicException('Controller error: path not found');
        }

        $route = array_shift($elements);
        if ($route === $this->name) {
            // excute request middlewares
            $requestMiddlewareResult = $this->executeRequestMiddlewares();
            if ($requestMiddlewareResult !== null) {
                return $requestMiddlewareResult;
            }
            if (count($elements) === 0) {
                // execute this default action
                $fullPath = $this->getFullPath();
                if ($this->action !== null) {
                    try {
                        $response = $this->action->execute();
                        // execute response middlewares
                        return $this->executeResponseMiddlewares($response);
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
                    $nextControllerResult = $nextController->execute($elements);
                    // execute response middlewares
                    return $this->executeResponseMiddlewares($nextControllerResult);
                } catch (NoSuchControllerException $e) {
                    // we do not have next controller
                    // execute dynamic action
                    if (count($elements) === 1) {
                        if ($this->dynamicAction === null) {
                            //1.2.3.1 - throw NotFoundException
                            throw new NotFoundException(ControllerInterface::NOT_FOUND_ERROR_MESSAGE);
                        }
                        try {
                            $response = $this->dynamicAction->execute($nextRoute);
                            // execute response middlewares
                            return $this->executeResponseMiddlewares($response);
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
        return $this->name;
    }

    public function getDescription(string $dynamicRoute = ''): string
    {
        if (strlen($dynamicRoute) === 0) {
            if ($this->action === null) {
                return $this->name;
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

    public function getChild(string $name): ControllerInterface
    {
        return $this->children[$name] ??
            throw new NoSuchControllerException('children with name: ' . $name . ' does not exist');
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getCurrentParent(): ControllerInterface|null
    {
        return $this->currentParent;
    }

    public function getDynamicRoutes(): array
    {
        if ($this->dynamicAction === null) {
            return [];
        }
        return $this->dynamicAction->getDynamicRoutes();
    }

    /** @todo check on usage in other classes */
    public function getFullPath(string $route = ''): array
    {
        $fullPath = [$this->name];
        if ($route !== '') {
            array_push($fullPath, $route);
        }
        $nextParrent = $this->currentParent;
        while ($nextParrent !== null) {
            array_unshift($fullPath, $nextParrent->getName());
            $nextParrent = $nextParrent->getCurrentParent();
        }
        return $fullPath;
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function requestMiddlewares(): array
    {
        return $this->requestMiddlewares;
    }

    public function responseMiddlewares(): array
    {
        return $this->responseMiddlewares;
    }

    public function setChild(ControllerInterface $child): Controller
    {
        $name = $child->getName();
        /** root controller must be one */
        if ($name === ControllerTreeInterface::ROOT_NAME) {
            throw new CantCreateControllerChainException(
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

    private function executeRequestMiddlewares(): ?ResponseInterface
    {
        foreach ($this->requestMiddlewares as $middleware) {
            $result = $middleware();
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }

    private function executeResponseMiddlewares(
        ResponseInterface $response
    ): ResponseInterface {
        $middlewareResponse = $response;
        foreach ($this->responseMiddlewares as $middleware) {
            $middlewareResponse = $middleware($middlewareResponse);
        }
        return $middlewareResponse;
    }
}

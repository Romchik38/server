<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Middlewares;

use Laminas\Diactoros\Stream;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\ControllersCollectionInterface;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;
use Romchik38\Server\Http\Routers\Middlewares\Result\PathMiddlewareResultInterface;

use function count;

class ControllerRouterMiddleware extends AbstractRouterMiddleware
{
    public function __construct(
        protected ControllersCollectionInterface $controllersCollection,
        protected ResponseFactoryInterface $responseFactory,
        protected readonly string $attributePathName,
        string $attributeName = 'controller_router_middleware'
    ) {
        parent::__construct($attributeName);
    }

    /**
     * @return null|ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): mixed
    {
        $allowedMethods = $this->controllersCollection->getMethods();
        if (count($allowedMethods) === 0) {
            return null;
        }

        $method = $request->getMethod();

        $rootController = $this->controllersCollection->getController($method);
        if ($rootController === null) {
            return $this->methodNotAllowed($allowedMethods);
        }

        $pathResult = $request->getAttribute($this->attributePathName);
        if (! $pathResult instanceof PathMiddlewareResultInterface) {
            return null;
        }

        try {
            $response = $rootController->handle($request->withAttribute(
                ControllerInterface::REQUEST_ELEMENTS_NAME,
                ($pathResult->getPath())()
            ));
            if ($method === 'HEAD') {
                $stream   = new Stream('php://temp');
                $response = $response->withBody($stream);
            }
            return $response;
        } catch (NotFoundException) {
            return null;
        }
    }

    /**
     * Set the result to 405 - Method Not Allowed
     *
     * @param array<int,string> $methods
     */
    protected function methodNotAllowed(array $methods): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(405);
        $body     = $response->getBody();
        $body->write('Method Not Allowed');
        $response = $response->withBody($body)
            ->withAddedHeader('Allow', $methods);
        return $response;
    }
}

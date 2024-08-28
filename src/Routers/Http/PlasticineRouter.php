<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;
use Romchik38\Server\Api\Router\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\RedirectInterface;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Api\Router\Http\RouterHeadersInterface;

class PlasticineRouter implements HttpRouterInterface
{
    protected array $headers;

    public function __construct(
        protected HttpRouterResultInterface $routerResult,
        protected array $controllers,
        array $headers,
        protected ControllerInterface | null $notFoundController = null,
        protected RedirectInterface|null $redirectService = null
    ) {
        $this->headers = $headers[$_SERVER['REQUEST_METHOD']] ?? [];
    }
    public function execute(): HttpRouterResultInterface
    {
        // 1. method check 
        $method = $_SERVER['REQUEST_METHOD'];
        if (array_key_exists($method, $this->controllers) === false) {
            return $this->methodNotAllowed();
        }

        /** @var ControllerInterface $rootController */
        $rootController = $this->controllers[$method];

        // 2. parse url
        [$url] = explode('?', $_SERVER['REQUEST_URI']);

        $elements = explode('/', $url);

        // two blank elements for /
        if (count($elements) === 2 && $elements[0] === '' && $elements[1] === '') {
            $elements = [$elements[0]];
        }

        // replace blank with root
        if ($elements[0] === '') {
            $elements[0] = 'root';
        }

        // 3. Exec
        try {
            $controllerResult = $rootController->execute($elements);

            $path = $controllerResult->getPath();
            $response = $controllerResult->getResponse();
            $type = $controllerResult->getType();

            $this->routerResult->setStatusCode(200)->setResponse($response);

            $this->setHeaders($path, $type);

            return $this->routerResult;

        } catch (NotFoundException $e) {
            return $this->pageNotFound();
        }
    }

    protected function methodNotAllowed(): HttpRouterResultInterface
    {
        $this->routerResult->setResponse('Method Not Allowed')
            ->setStatusCode(405)
            ->setHeaders([
                ['Allow:' . implode(', ', array_keys($this->controllers))]
            ]);
        return $this->routerResult;
    }

    protected function pageNotFound()
    {
        $response = 'Error 404 from router - Page not found';
        if ($this->notFoundController !== null) {
            $response = $this->notFoundController->execute(['404'])->getResponse();
        }
        $this->routerResult->setStatusCode(404)
            ->setResponse($response);
        return $this->routerResult;
    }

    protected function setHeaders(array $path, string $type) {
        $pathString = implode(ControllerInterface::PATH_SEPARATOR, $path);
        $header = $this->headers[$pathString] ?? null;

        if ($header === null && $type === ActionInterface::TYPE_DYNAMIC_ACTION) {
            $dynamicPath = array_slice($path, 0, count($path)-1);
            array_push($dynamicPath, ControllerInterface::PATH_DYNAMIC_ALL);
            $dynamicPathString = implode(ControllerInterface::PATH_SEPARATOR, $dynamicPath);
            $header = $this->headers[$dynamicPathString] ?? null;
        }

        if ($header !== null) {
            /** @var RouterHeadersInterface $header */
            $header->setHeaders($this->routerResult, $path);
        }
    }
}

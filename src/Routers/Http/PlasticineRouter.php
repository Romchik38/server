<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Romchik38\Server\Api\Controllers\Actions\ActionInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;
use Romchik38\Server\Api\Router\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Api\Router\Http\RouterHeadersInterface;
use Romchik38\Server\Api\Services\Request\Http\RequestInterface;
use Romchik38\Server\Api\Services\SitemapInterface;

class PlasticineRouter implements HttpRouterInterface
{
    protected array $headers;

    public function __construct(
        protected HttpRouterResultInterface $routerResult,
        protected array $controllers,
        protected RequestInterface $request,
        array $headers = [],
        protected ControllerInterface | null $notFoundController = null,
        protected RedirectInterface|null $redirectService = null
    ) {
        $this->headers = $headers[$request->getMethod()] ?? [];
    }
    public function execute(): HttpRouterResultInterface
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();
        $path = $uri->getPath();
        [$url] = explode('?', $path);

        // 1. method check 
        if (array_key_exists($method, $this->controllers) === false) {
            return $this->methodNotAllowed();
        }

        // 2. redirect check
        if ($this->redirectService !== null) {
            $redirectResult = $this->redirectService->execute($url, $method);
            if ($redirectResult !== null) {
                return $this->redirect($redirectResult);
            }
        }

        /** @var ControllerInterface $rootController */
        $rootController = $this->controllers[$method];

        // 3. parse url
        $elements = explode('/', $url);

        // two blank elements for /
        if (count($elements) === 2 && $elements[0] === '' && $elements[1] === '') {
            $elements = [''];
        }

        // replace blank with root
        if ($elements[0] === '') {
            $elements[0] = SitemapInterface::ROOT_NAME;
        }

        // 4. Exec
        try {
            $controllerResult = $rootController->execute($elements);

            $path = $controllerResult->getPath();
            $response = $controllerResult->getResponse();
            $type = $controllerResult->getType();

            $this->routerResult->setStatusCode(200)->setResponse($response);
            return $this->setHeaders($path, $type);
        } catch (NotFoundException $e) {
            return $this->pageNotFound();
        }
    }

    /**
     * set the result to 405 - Method Not Allowed
     */
    protected function methodNotAllowed(): HttpRouterResultInterface
    {
        $this->routerResult->setResponse('Method Not Allowed')
            ->setStatusCode(405)
            ->setHeaders([
                ['Allow:' . implode(', ', array_keys($this->controllers))]
            ]);
        return $this->routerResult;
    }

    /**
     * set the result to 404 - Not Found
     */
    protected function pageNotFound(): HttpRouterResultInterface
    {
        $response = 'Error 404 from router - Page not found';
        if ($this->notFoundController !== null) {
            $response = $this->notFoundController->execute(['404'])->getResponse();
        }
        $this->routerResult->setStatusCode(404)
            ->setResponse($response);
        return $this->routerResult;
    }

    /** 
     * set headers for actions
     */
    protected function setHeaders(array $path, string $type): HttpRouterResultInterface
    {
        $pathString = implode(ControllerInterface::PATH_SEPARATOR, $path);
        $header = $this->headers[$pathString] ?? null;

        if ($header === null && $type === ActionInterface::TYPE_DYNAMIC_ACTION) {
            $dynamicPath = array_slice($path, 0, count($path) - 1);
            array_push($dynamicPath, ControllerInterface::PATH_DYNAMIC_ALL);
            $dynamicPathString = implode(ControllerInterface::PATH_SEPARATOR, $dynamicPath);
            $header = $this->headers[$dynamicPathString] ?? null;
        }

        if ($header !== null) {
            /** @var RouterHeadersInterface $header */
            $header->setHeaders($this->routerResult, $path);
        }

        return $this->routerResult;
    }

    /**
     * Set a redirect to the same site with founded url and status code
     */
    protected function redirect(RedirectResultDTOInterface $redirectResult): HttpRouterResultInterface
    {
        $uri = $redirectResult->getRedirectLocation();
        $statusCode = $redirectResult->getStatusCode();
        $this->routerResult->setHeaders([
            [
                'Location: ' . $uri,
                true,
                $statusCode
            ]
        ]);

        return $this->routerResult;
    }
}

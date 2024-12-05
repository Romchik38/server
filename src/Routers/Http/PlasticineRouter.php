<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;
use Romchik38\Server\Api\Routers\Http\ControllersCollectionInterface;
use Romchik38\Server\Api\Routers\Http\HeadersCollectionInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Api\Routers\Http\RouterHeadersInterface;
use Romchik38\Server\Api\Services\Request\Http\RequestInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;

class PlasticineRouter implements HttpRouterInterface
{
    protected array $headers;

    public function __construct(
        protected HttpRouterResultInterface $routerResult,
        /** @todo replace with controller collection */
        protected ControllersCollectionInterface $controllersCollection,
        protected RequestInterface $request,
        protected HeadersCollectionInterface|null $headersCollection = null,
        protected ControllerInterface | null $notFoundController = null,
        protected RedirectInterface|null $redirectService = null
    ) {}
    public function execute(): HttpRouterResultInterface
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();
        $path = $uri->getPath();
        [$url] = explode('?', $path);

        // 1. method check 
        $rootController = $this->controllersCollection->getController($method);
        if (is_null($rootController)) {
            return $this->methodNotAllowed($this->controllersCollection->getMethods());
        }

        // 2. redirect check
        if ($this->redirectService !== null) {
            $redirectResult = $this->redirectService->execute($url, $method);
            if ($redirectResult !== null) {
                return $this->redirect($redirectResult);
            }
        }

        // 3. parse url
        $elements = explode('/', $url);

        // two blank elements for /
        if (count($elements) === 2 && $elements[0] === '' && $elements[1] === '') {
            $elements = [''];
        }

        // replace blank with root
        if ($elements[0] === '') {
            $elements[0] = ControllerTreeInterface::ROOT_NAME;
        }

        // 4. Exec
        try {
            $controllerResult = $rootController->execute($elements);

            $path = $controllerResult->getPath();
            $response = $controllerResult->getResponse();
            $type = $controllerResult->getType();

            $this->routerResult->setStatusCode(200)->setResponse($response);
            $headerPath = $this->getHeaderPath($path);
            /** @var RouterHeadersInterface|null  $header */
            $header = $this->headersCollection->getHeader($method, $headerPath, $type);
            if ($header !== null) {
                $header->setHeaders($this->routerResult, $path);
            }

            return $this->routerResult;
        } catch (NotFoundException $e) {
            return $this->pageNotFound();
        }
    }

    /**
     * set the result to 405 - Method Not Allowed
     */
    protected function methodNotAllowed(array $allowedMethods): HttpRouterResultInterface
    {
        $allowedMethodsAsString = implode(', ', $allowedMethods);
        $this->routerResult->setResponse('Method Not Allowed')
            ->setStatusCode(405)
            ->setHeaders([
                ['Allow:' . $allowedMethodsAsString]
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

    /** 
     * set headers for actions
     */
    protected function getHeaderPath(array $path): string
    {
        return implode(ControllerInterface::PATH_SEPARATOR, $path);
    }
}

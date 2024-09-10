<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;          

use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Results\Http\HttpRouterResultInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Api\Routers\Http\RouterHeadersInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Request\Http\RequestInterface;
use Romchik38\Server\Routers\Errors\RouterProccessError;

class DynamicRootRouter implements HttpRouterInterface
{

    /**
     * @param array $headers Is a function, that accepts the rootName and returns an instance of HeadersCollectionInterface
     */
    public function __construct(
        protected HttpRouterResultInterface $routerResult,
        protected RequestInterface $request,
        protected DynamicRootInterface $dynamicRootService,
        protected array $actionListCallback,
        protected array $headers = [],
        protected ControllerInterface | null $notFoundController = null,
        protected RedirectInterface|null $redirectService = null
    ) {
        /** headers check */
        if(count($headers) > 0) {
            if(is_callable($headers[0]) === false) {
                throw new RouterProccessError('1st item of headers array must be a callable');
            }
        }
    }
    public function execute(): HttpRouterResultInterface
    {
        // 0. define
        $uri = $this->request->getUri();
        $scheme = $uri->getScheme();
        $host = $uri->getHost();
        $method = $this->request->getMethod();
        $path = $uri->getPath();
        [$url] = explode('?', $path);

        $defaultRoot = $this->dynamicRootService->getDefaultRoot();
        $rootList = $this->dynamicRootService->getRootNames();

        // 1. parse url
        $elements = explode('/', $url);

        // two blank elements for /
        if (count($elements) === 2 && $elements[0] === '' && $elements[1] === '') {
            $elements = [''];
        }

        // delete first blank item
        array_shift($elements);

        // 2. for / redirect to default root
        if (count($elements) === 0) {
            return $this->routerResult->setHeaders([[
                'Location: ' . $scheme . RedirectInterface::SCHEME_HOST_DELIMITER
                    . $host . '/' . $defaultRoot->getName(),
                true,
                301
            ]]);
        }

        $rootName = $elements[0];

        // 3. try to redirect to defaultRoot + path
        if (array_search($rootName, $rootList, true) === false) {
            return $this->routerResult->setHeaders([[
                'Location: ' . $scheme . RedirectInterface::SCHEME_HOST_DELIMITER
                    . $host . '/' . $defaultRoot->getName() . $path,
                true,
                301
            ]]);
        }

        // 4. Create a dynamic root
        /** @todo replace callback with class */
        $controllers = ($this->actionListCallback[0])($rootName);

        // 5. method check 
        if (array_key_exists($method, $controllers) === false) {
            return $this->methodNotAllowed($controllers);
        }

        // 6. redirect check
        if ($this->redirectService !== null) {
            $redirectResult = $this->redirectService->execute($url, $method);
            if ($redirectResult !== null) {
                return $this->routerResult->setHeaders([
                    [
                        'Location: ' . $scheme . RedirectInterface::SCHEME_HOST_DELIMITER
                            . $host . $redirectResult->getRedirectLocation(),
                        true,
                        $redirectResult->getStatusCode()
                    ]
                ]);
            }
        }

        /**
         * 7. set current root
         * 
         * - the check may be ommited, because early we did check #3 with $rootList
         *   and redirected all requests which starts with items not in the $rootList 
         * - but we can't set $rootName which is not in the list because of something ...
         * - so there is the check:
         */
        $isSetCurrentRoot = $this->dynamicRootService->setCurrentRoot($rootName);
        if ($isSetCurrentRoot === false) {
            throw new RouterProccessError('Can\'t set current dynamic root with name: ' . $rootName);
        }

        /** @var ControllerInterface $rootController */
        $rootController = $controllers[$method];

        try {
            // 8. Exec
            $controllerResult = $rootController->execute($elements);

            $path = $controllerResult->getPath();
            $response = $controllerResult->getResponse();
            $type = $controllerResult->getType();

            $this->routerResult->setStatusCode(200)->setResponse($response);

            // 9. Set headers
            if (count($this->headers) > 0) {
                $callback = $this->headers[0];
                /** @var HeadersCollectionInterface $headerService */
                $headerService = $callback($rootName);
                $headerPath = $this->getHeaderPath($path);
                /** @var RouterHeadersInterface|null  $header */
                $header = $headerService->getHeader($method, $headerPath, $type);
                if($header !== null) {
                    $header->setHeaders($this->routerResult, $path);
                }
            }
            // 10. Exit
            return $this->routerResult;
        } catch (NotFoundException $e) {
            // 11. Show page not found
            return $this->pageNotFound();
        }
    }

    /**
     * set the result to 405 - Method Not Allowed
     */
    protected function methodNotAllowed(array $controllers): HttpRouterResultInterface
    {
        $this->routerResult->setResponse(HttpRouterResultInterface::METHOD_NOT_ALLOWED_RESPONSE)
            ->setHeaders([
                [
                    'Allow:' . implode(', ', array_keys($controllers)),
                    true,
                    HttpRouterResultInterface::METHOD_NOT_ALLOWED_CODE
                ]
            ]);
        return $this->routerResult;
    }

    /**
     * set the result to 404 - Not Found
     */
    protected function pageNotFound(): HttpRouterResultInterface
    {
        $response = HttpRouterResultInterface::NOT_FOUND_RESPONSE;
        if ($this->notFoundController !== null) {
            $response = $this->notFoundController->execute(
                [HttpRouterResultInterface::NOT_FOUND_STATUS_CODE]
            )->getResponse();
        }
        $this->routerResult->setStatusCode(HttpRouterResultInterface::NOT_FOUND_STATUS_CODE)
            ->setResponse($response);
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

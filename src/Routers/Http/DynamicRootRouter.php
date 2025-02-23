<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Routers\Http\ControllersCollectionInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\DynamicRoot\DynamicRootInterface;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Routers\Errors\RouterProccessError;

use function array_search;
use function array_shift;
use function count;
use function explode;

class DynamicRootRouter implements HttpRouterInterface
{
    use RouterTrait;

    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected ServerRequestInterface $request,
        protected DynamicRootInterface $dynamicRootService,
        protected ControllersCollectionInterface $controllersCollection,
        protected ControllerInterface | null $notFoundController = null,
        protected RedirectInterface|null $redirectService = null
    ) {
    }

    public function execute(): ResponseInterface
    {
        // 0. define
        $uri    = $this->request->getUri();
        $scheme = $uri->getScheme();
        $host   = $uri->getHost();
        $method = $this->request->getMethod();
        $path   = $uri->getPath();
        [$url]  = explode('?', $path);

        $defaultRoot = $this->dynamicRootService->getDefaultRoot();
        $rootList    = $this->dynamicRootService->getRootNames();

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
            $redirectLine = $scheme
                . RedirectInterface::SCHEME_HOST_DELIMITER
                . $host
                . '/'
                . $defaultRoot->getName();
            return ($this->responseFactory->createResponse(301))
                ->withHeader('Location', $redirectLine);
        }

        $rootName = $elements[0];

        // 3. try to redirect to defaultRoot + path
        if (array_search($rootName, $rootList, true) === false) {
            $redirectLinePlusPath = $scheme
                . RedirectInterface::SCHEME_HOST_DELIMITER
                . $host
                . '/'
                . $defaultRoot->getName()
                . $path;
            return ($this->responseFactory->createResponse(301))
                ->withHeader('Location', $redirectLinePlusPath);
        }

        // 4. Get controller
        $controller = $this->controllersCollection->getController($method);

        // 5. method check
        if ($controller === null) {
            return $this->methodNotAllowed($this->controllersCollection->getMethods());
        }

        // 6. redirect check
        if ($this->redirectService !== null) {
            $redirectResult = $this->redirectService->execute($url, $method);
            if ($redirectResult !== null) {
                $url = $this->normalizeRedirectUrl(
                    $redirectResult->getRedirectLocation(),
                    $host,
                    $scheme
                );
                return $this->redirect($url, $redirectResult);
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

        /**
         * 8. replace $rootName with 'root' */
        $elements[0] = ControllerTreeInterface::ROOT_NAME;

        try {
            // 9. Exec
            $controllerResult = $controller->execute($elements);
            return $controllerResult->getResponse();
        } catch (NotFoundException) {
            // 11. Show page not found
            return $this->pageNotFound();
        }
    }

     /**
      * Set a redirect to the same site with founded url and status code
      */
    protected function redirect(
        string $url,
        RedirectResultDTOInterface $redirectResult
    ): ResponseInterface {
        $statusCode = $redirectResult->getStatusCode();
        return $this->responseFactory->createResponse($statusCode)
            ->withHeader('Location', $url);
    }
}

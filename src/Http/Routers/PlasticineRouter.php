<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Http\Controller\ControllerInterface;
use Romchik38\Server\Http\Controller\ControllersCollectionInterface;
use Romchik38\Server\Http\Controller\Errors\NotFoundException;

use function count;
use function explode;

class PlasticineRouter implements HttpRouterInterface
{
    use RouterTrait;

    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected readonly ControllersCollectionInterface $controllersCollection,
        protected readonly ServerRequestInterface $request,
        protected readonly ControllerInterface | null $notFoundController = null,
        protected readonly RedirectInterface|null $redirectService = null
    ) {
    }

    public function execute(): ResponseInterface
    {
        $uri    = $this->request->getUri();
        $method = $this->request->getMethod();
        $host   = $uri->getHost();
        $scheme = $uri->getScheme();
        $path   = $uri->getPath();
        [$url]  = explode('?', $path);

        // 1. method check
        $rootController = $this->controllersCollection->getController($method);
        if ($rootController === null) {
            return $this->methodNotAllowed($this->controllersCollection->getMethods());
        }

        // 2. redirect check
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

        // 3. parse url
        $elements = explode('/', $url);

        // two blank elements for /
        if (count($elements) === 2 && $elements[0] === '' && $elements[1] === '') {
            $elements = [''];
        }

        // replace blank with root
        if ($elements[0] === '') {
            $elements[0] = ControllerInterface::ROOT_NAME;
        }

        // 4. Exec
        try {
            return $rootController->execute($elements);
        } catch (NotFoundException) {
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
        return ($this->responseFactory->createResponse($statusCode))
            ->withHeader('Location', $url);
    }
}

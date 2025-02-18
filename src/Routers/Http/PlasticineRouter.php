<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Api\Controllers\ControllerInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Routers\Http\ControllersCollectionInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Controllers\Errors\NotFoundException;
use Romchik38\Server\Api\Services\Mappers\ControllerTreeInterface;

class PlasticineRouter implements HttpRouterInterface
{
    public function __construct(
        protected readonly ControllersCollectionInterface $controllersCollection,
        protected readonly ServerRequestInterface $request,
        protected readonly ControllerInterface | null $notFoundController = null,
        protected readonly RedirectInterface|null $redirectService = null
    ) {
    }

    public function execute(): ResponseInterface
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
            return $controllerResult->getResponse();
        } catch (NotFoundException) {
            return $this->pageNotFound();
        }
    }

    /**
     * set the result to 405 - Method Not Allowed
     */
    protected function methodNotAllowed(array $allowedMethods): ResponseInterface
    {
        $response = new Response();
        $body = $response->getBody();
        $body->write('Method Not Allowed');
        $response = $response->withStatus(405)->withBody($body)
            ->withAddedHeader('Allow', $allowedMethods);
        return $response;
    }

    /**
     * set the result to 404 - Not Found
     */
    protected function pageNotFound(): ResponseInterface
    {
        if ($this->notFoundController !== null) {
            $response = $this->notFoundController->execute(['404'])->getResponse();
        } else {
            $response = new Response();
            $body = $response->getBody();
            $body->write('Error 404 from router - Page not found');
            $response = $response->withBody($body);
        }
        $response = $response->withStatus(404);
        return $response;
    }

    /**
     * Set a redirect to the same site with founded url and status code
     */
    protected function redirect(RedirectResultDTOInterface $redirectResult): ResponseInterface
    {
        $uri = $redirectResult->getRedirectLocation();
        $statusCode = $redirectResult->getStatusCode();
        $response = (new Response())->withStatus($statusCode)->withHeader('Location', $uri);
        return  $response;
    }
}

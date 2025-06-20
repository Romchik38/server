<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function sprintf;
use function str_starts_with;

trait RouterTrait
{
    protected function normalizeRedirectUrl(
        string $url,
        string $authority,
        string $scheme
    ): string {
        if (str_starts_with($url, 'http') || str_starts_with($url, 'https')) {
            return $url;
        }
        if (str_starts_with($url, '/')) {
            return sprintf(
                '%s://%s%s',
                $scheme,
                $authority,
                $url
            );
        }
        return sprintf(
            '%s://%s/%s',
            $scheme,
            $authority,
            $url
        );
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

     /**
      * set the result to 404 - Not Found
      */
    protected function pageNotFound(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->notFoundHandler !== null) {
            $response = $this->notFoundHandler->handle($request);
            $response = $response->withStatus(404);
        } else {
            $response = $this->responseFactory->createResponse(404);
            $body     = $response->getBody();
            $body->write(HttpRouterInterface::NOT_FOUND_MESSAGE);
            $response = $response->withBody($body);
        }
        return $response;
    }
}

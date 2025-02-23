<?php

declare(strict_types=1);

namespace Romchik38\Server\Routers\Http;

use Psr\Http\Message\ResponseInterface;
use Romchik38\Server\Api\Routers\Http\HttpRouterInterface;

use function sprintf;
use function str_starts_with;

trait RouterTrait
{
    protected function normalizeRedirectUrl(
        string $url,
        string $host,
        string $scheme
    ): string {
        if (str_starts_with($url, 'http') || str_starts_with($url, 'https')) {
            return $url;
        }
        if (str_starts_with($url, '/')) {
            return sprintf(
                '%s://%s%s',
                $scheme,
                $host,
                $url
            );
        }
        return sprintf(
            '%s://%s/%s',
            $scheme,
            $host,
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
    protected function pageNotFound(): ResponseInterface
    {
        if ($this->notFoundController !== null) {
            $response = $this->notFoundController->execute([
                HttpRouterInterface::NOT_FOUND_CONTROLLER_NAME,
            ])->getResponse();
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

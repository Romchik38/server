<?php

declare(strict_types=1);

namespace Romchik38\Server\Http\Routers\Handlers\Redirect;

use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\CantCreateRedirectException;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\NoSuchRedirectException;
use Romchik38\Server\Http\Routers\Handlers\Redirect\Exceptions\RepositoryException;

use function in_array;
use function sprintf;
use function str_starts_with;

/**
 * Request handler
 */
class Redirect implements RedirectInterface
{
    protected string $scheme;
    protected string $host;
    protected string $authority;

    /** @throws CantCreateRedirectException */
    public function __construct(
        protected readonly RepositoryInterface $redirectRepository,
        protected readonly RedirectResultDTOFactoryInterface $redirectResultDtoFactory,
        ServerRequestInterface $request
    ) {
        $uri    = $request->getUri();
        $scheme = $uri->getScheme();
        if (in_array($scheme, RedirectInterface::ALLOWED_SCHEMAS, true) === false) {
            throw new CantCreateRedirectException('Scheme:' . $scheme . ' not allowed');
        }
        $this->scheme = $scheme;

        $host = $uri->getHost();
        if ($host === '') {
            throw new CantCreateRedirectException('Host name can\'t be empty');
        }
        $this->host = $host;

        $this->authority = $uri->getAuthority();
    }

    public function execute(string $redirectFrom, string $method): RedirectResultDTOInterface|null
    {
        try {
            $redirectUrl = $this->redirectRepository->checkUrl($redirectFrom, $method);

            $uri = $this->normalizeRedirectUrl(
                $redirectUrl->getRedirectTo(),
                $this->authority,
                $this->scheme
            );

            return $this->redirectResultDtoFactory->create(
                $uri,
                $redirectUrl->getRedirectCode()
            );
        } catch (NoSuchRedirectException) {
            return null;
        } catch (RepositoryException $e) {
            throw new CantCreateRedirectException($e->getMessage());
        }
    }

    /** Used from router trait */
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
}

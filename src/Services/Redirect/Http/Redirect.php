<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Redirect\Http;

use Psr\Http\Message\ServerRequestInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Models\Redirect\RedirectRepositoryInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Services\Errors\CantCreateRedirectException;

use function in_array;

/**
 * Redirect to the same scheme://host
 */
class Redirect implements RedirectInterface
{
    protected string $scheme;
    protected string $host;

    public function __construct(
        protected readonly RedirectRepositoryInterface $redirectRepository,
        protected readonly RedirectResultDTOFactoryInterface $redirectResultDTOFactory,
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
    }

    public function execute(string $redirectFrom, string $method): RedirectResultDTOInterface|null
    {
        try {
            $redirectUrl = $this->redirectRepository->checkUrl($redirectFrom, $method);

            $uri = $this->scheme
                . $this::SCHEME_HOST_DELIMITER
                . $this->host
                . $redirectUrl->getRedirectTo();

            return $this->redirectResultDTOFactory->create(
                $uri,
                $redirectUrl->getRedirectCode()
            );
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}

<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Redirect\Http;

use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Api\Models\Redirect\RedirectRepositoryInterface;
use Romchik38\Server\Api\Services\Request\Http\RequestInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;
use Romchik38\Server\Services\Errors\CantCreateRedirectException;

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
        RequestInterface $request
    ) {
        $uri = $request->getUri();
        $scheme = $uri->getScheme();
        if (in_array($scheme, RedirectInterface::ALLOWED_SCHEMAS, true) === false) {
            throw new CantCreateRedirectException('Scheme:' . $scheme . ' not allowed');
        }
        $this->scheme = $request;

        $host = $uri->getHost();
        if ($host === '') {
            throw new CantCreateRedirectException('Host name can\'t be empty');
        }
        $this->host = $host;
    }

    public function execute(string $url, string $method): RedirectResultDTOInterface|null
    {
        try {
            $redirectUrl = $this->redirectRepository->checkUrl($url, $method);

            $uri = $this->scheme
                . $this::SCHEME_HOST_DELIMITER
                . $this->host
                . $redirectUrl->getRedirectTo();

            $redirectResult = $this->redirectResultDTOFactory->create(
                $uri,
                $redirectUrl->getRedirectCode()
            );

            return $redirectResult;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}

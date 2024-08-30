<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Redirect\Http;

use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOFactoryInterface;
use Romchik38\Server\Api\Models\DTO\RedirectResult\Http\RedirectResultDTOInterface;
use Romchik38\Server\Api\Services\Redirect\Http\RedirectInterface;
use Romchik38\Server\Api\Models\Redirect\RedirectRepositoryInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

class Redirect implements RedirectInterface
{
    protected string $scheme;
    protected string $host;

    public function __construct(
        protected RedirectRepositoryInterface $redirectRepository,
        protected RedirectResultDTOFactoryInterface $redirectResultDTOFactory
    ) {
        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->host = $_SERVER['HTTP_HOST'];
    }

    public function execute(string $url, string $method): RedirectResultDTOInterface|null
    {
        try {
            $redirectUrl = $this->redirectRepository->checkUrl($url, $method);

            /** @todo implement schema and host in the RedirectResultDTOInterface */
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

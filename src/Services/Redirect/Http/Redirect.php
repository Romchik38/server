<?php

declare(strict_types=1);

namespace Romchik38\Server\Services\Redirect\Http;

use Romchik38\Server\Api\Services\RedirectInterface;
use Romchik38\Server\Api\Models\RedirectRepositoryInterface;
use Romchik38\Server\Models\Errors\NoSuchEntityException;

class Redirect implements RedirectInterface
{

    protected bool $redirect = false;
    protected string $redirectLocation = '';
    protected int $statusCode = 0;

    public function __construct(
        protected RedirectRepositoryInterface $redirectRepository
    ) {
    }

    public function execute($action): void
    {
        try {
            $redirectUrl = $this->redirectRepository->checkUrl($action);
//            if ($redirectUrl !== '') {
                $this->redirect = true;
                $this->redirectLocation = 'Location: ' . $_SERVER['REQUEST_SCHEME'] . '://'
                                . $_SERVER['HTTP_HOST']
                                . $redirectUrl->getRedirectTo();
                $this->statusCode = $redirectUrl->getRedirectCode();
//            }
        } catch (NoSuchEntityException $e) {
            // return empty result
        }
    }
    public function isRedirect(): bool
    {
        return $this->redirect;
    }

    public function getRedirectLocation(): string {
        return $this->redirectLocation;
    }
    public function getStatusCode(): int {
        return $this->statusCode;
    }
}
